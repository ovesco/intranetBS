<?php

namespace AppBundle\Controller;

/* Symfony */
use AppBundle\Entity\Creance;
use AppBundle\Utils\Response\ResponseFactory;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/* Routing */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Utils\Menu\Menu;

/* Entity */
use AppBundle\Entity\Facture;
use AppBundle\Entity\Payement;

/* Elastica */
use AppBundle\Search\Facture\FactureRepository;

/* Form */
use AppBundle\Search\Payement\PayementSearchType;
use AppBundle\Form\Payement\PayementAddMultipleType;
use AppBundle\Form\Payement\PayementUploadFileType;
use AppBundle\Form\Payement\PayementAddType;
use AppBundle\Form\Payement\PayementValidationType;

/* Other */
use AppBundle\Search\Payement\PayementSearch;
use AppBundle\Search\Payement\PayementRepository;
use AppBundle\Utils\ListUtils\ListKey;

/* Services */
use AppBundle\Utils\Finances\PayementFileParser;
use AppBundle\Utils\ListUtils\ListStorage;
use AppBundle\Utils\SessionTools\Notification;
use AppBundle\Utils\Response\AjaxResponseFactory;
use AppBundle\Search\Mode;



/**
 * Class PayementController
 * @package AppBundle\Controller
 * @Route("/intranet/payement")
 */
class PayementController extends Controller
{

    /**
     * Page for searching payement
     *
     * @Route("/search", options={"expose"=true})
     * @Menu("Recherche de payement",block="finances",order=3,icon="search")
     * @param Request $request
     * @return Response
     * @Template("AppBundle:Payement:page_recherche.html.twig")
     */
    public function searchAction(Request $request){

        $payementSearch = new PayementSearch();

        $searchForm = $this->createForm(new PayementSearchType,$payementSearch);

        /** @var ListStorage $sessionContainer */
        $sessionContainer = $this->get('list_storage');
        $sessionContainer->setRepository(ListKey::PAYEMENTS_SEARCH_RESULTS,'AppBundle:Payement');


        $searchForm->handleRequest($request);

        if ($searchForm->isValid()) {

            $payementSearch = $searchForm->getData();

            $elasticaManager = $this->container->get('fos_elastica.manager');

            /** @var PayementRepository $repository */
            $repository = $elasticaManager->getRepository('AppBundle:Payement');

            $results = $repository->search($payementSearch);

            //get the search mode
            $mode = $searchForm->get(Mode::FORM_FIELD)->getData();
            switch($mode)
            {
                case Mode::INCLUDE_PREVIOUS: //include new results with the previous
                    $sessionContainer->addObjects(ListKey::PAYEMENTS_SEARCH_RESULTS,$results);
                    break;
                case Mode::EXCLUDE_PREVIOUS: //exclude new results to the previous
                    $sessionContainer->removeObjects(ListKey::PAYEMENTS_SEARCH_RESULTS,$results);
                    break;
                case Mode::STANDARD:
                default:
                    $sessionContainer->setObjects(ListKey::PAYEMENTS_SEARCH_RESULTS,$results);

            }

        }

        return array('searchForm'=>$searchForm->createView(),'list_key'=>ListKey::PAYEMENTS_SEARCH_RESULTS);
    }

    /**
     * Return modal of the payement
     *
     * @Route("/show/{payement}", options={"expose"=true})
     * @param Payement $payement
     * @ParamConverter("payement", class="AppBundle:Payement")
     * @Template("AppBundle:Payement:showModal.html.twig")
     * @return Response
     */
    public function showAction(Payement $payement){
        return array('payement'=>$payement);
    }


    /**
     * Page for adding new payement (manualy or by uploading file)
     *
     * @Route("/add", options={"expose"=true})
     * @Menu("Ajout de payements",block="finances",order=4,icon="add")
     * @param Request $request
     * @Template("AppBundle:Payement:page_saisie.html.twig")
     * @return Response
     */
    public function addAction(Request $request){

        return array();
    }


    /**
     * Form for adding manualy payments
     *
     * @Route("/create", options={"expose"=true})
     * @param Request $request
     * @Template("AppBundle:Payement:form_add_manually.html.twig")
     * @return Response
     */
    public function createAction(Request $request){

        $payement = new Payement();
        $form = $this->createForm(new PayementAddType(),$payement,array('action'=>$this->generateUrl('app_payement_create')));

        $form->handleRequest($request);

        if($form->isValid()){

            $payement->setDate(new \DateTime('now'));
            $payement = $this->get('app.payement.check')->validation($payement);

            /*
             * Si le payement est accepté, on process un nouveaux formulaire
             * Et on renvoie un feedback sur le payement prédédent
             */
            $nextPayement = new Payement();
            $form = $this->createForm(new PayementAddType(),$nextPayement,array('action'=>$this->generateUrl('app_payement_create')));
            return array('form'=>$form->createView(),'previousPayement'=>$payement);
        }

        return array('form'=>$form->createView());
    }




    /**
     * Page for validation of payements
     *
     * @Route("/validation", options={"expose"=true})
     * @Menu("Validation des payements",block="finances",order=5,icon="checkmark")
     * @param Request $request
     * @Template("AppBundle:Payement:page_validation.html.twig")
     * @return Response
     */
    public function validationAction(Request $request){

        return array();
    }



    /**
     * Validation form
     *
     * @Route("/validation_form/{payement}", options={"expose"=true})
     * @param Request $request
     * @param Payement $payement
     * @ParamConverter("payement", class="AppBundle:Payement")
     * @Template("AppBundle:Payement:validationForm.html.twig")
     * @return Response
     */
    public function validationFormAction(Request $request,Payement $payement)
    {
        $form  = $this->createForm(new PayementValidationType(),
            $payement,array('action'=>$this->generateUrl('app_payement_validationform',array('payement'=>$payement->getId()))));

        $form->handleRequest($request);

        if($form->isValid()) {

            $message = 'Payement validé';

            /*
             * On test ici que le champ supplémentaire premetant la création
             * d'une cérance à été mit (uniquement pour les facture payé insuffisement)
             *
             */
            if($form->has("new_creance"))
            {
                $newCreance = $form->get("new_creance")->getData();

                /*
                 * On test si l'utilisateur soit faire une cérance de compensation
                 */
                if($newCreance){

                    $montant = $payement->getFacture()->getMontantEmis() - $payement->getMontantRecu();

                    /*
                     * On crée la créance de compensation
                     */
                    $creance = new Creance();
                    $creance
                        ->setDateCreation(new \DateTime('now'))
                        ->setMontantEmis($montant)
                        ->setTitre('Compensation montant insuffisant facture num.'.$payement->getFacture()->getId());

                    $payement->getFacture()->getDebiteur()->addCreance($creance);

                    $this->get('app.repository.creance')->save($creance);

                    $message = $message . ' et facture de compensation crée.';
                }
            }

            /*
             * On valide le payment définitivement
             */
            $payement->setValidated(true);

            $this->get('app.repository.payement')->save($payement);
            $this->get('app.notification_bag')->addNotification(new Notification($message,Notification::SUCCESS));

            return AjaxResponseFactory::ok(AjaxResponseFactory::POST_ACTION_RELOAD);

        }

        return array('form'=>$form->createView(),'payement'=>$payement);

    }

    /**
     * delete a payement
     *
     * @Route("/remove/{payement}", options={"expose"=true})
     * @param Payement $payement
     * @ParamConverter("payement", class="AppBundle:Payement")
     * @return Response
     */
    public function removeAction(Payement $payement){

        if($payement->isRemovable())
        {
            $this->get('app.repository.payement')->remove($payement);

            return ResponseFactory::ok('Payement supprimé');
        }
        return ResponseFactory::conflict('payement liée à une facture');
    }


}