<?php

namespace AppBundle\Controller;

/* Symfony */
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
use AppBundle\Form\FactureRepartitionType;
use AppBundle\Form\Payement\PayementSearchType;
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

            //set results in session
            $sessionContainer->setObjects(ListKey::PAYEMENTS_SEARCH_RESULTS,$results);

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
        $form  = $this->createForm(new PayementValidationType(),$payement);

        $form->handleRequest($request);

        if($form->isValid()) {

            $message = 'Payement validé';

            $newFacture = $form->get("new_facture")->getData();

            if($newFacture){
                $message = $message . ' et facture de compensation crée.';
            }

            $this->get('app.repository.payement')->save($payement);
            //$this->get('session')->getFashBag()->add('notice',$message);

            return ResponseFactory::ok();

        }

        return array('form'=>$form->createView(),'payement'=>$payement);

    }

    /**
     * delete a payement
     *
     * @Route("/delete/{payement}", options={"expose"=true})
     * @param Payement $payement
     * @ParamConverter("payement", class="AppBundle:Payement")
     * @return Response
     */
    public function deleteAction(Payement $payement){

        if((!$payement->isValidated()) || ($payement->getFacture() == null))
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($payement);
            $em->flush();

            $response = new Response();
            return $response->setStatusCode(200);//OK
        }
        $response = new Response();
        return $response->setStatusCode(409);//Conflict
    }



















































    /**
     * @Route("/repartition_payement", name="interne_finances_payement_repartition_ajax", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function getPayementRepartitionFormAjaxAction(Request $request){

        if($request->isXmlHttpRequest()) {

            $idPayement = $request->request->get('idPayement');

            $em = $this->getDoctrine()->getManager();
            $payement = $em->getRepository('AppBundle:Payement')->find($idPayement);


            if($payement != null){

                $facture = $em->getRepository('AppBundle:Facture')->find($payement->getIdFacture());


                $repartitionForm = $this->createForm(new FactureRepartitionType(),$facture);

                return $this->render('AppBundle:Payement:modalRepartitionForm.html.twig',
                    array('form'=>$repartitionForm->createView(),'payement'=>$payement,'facture'=>$facture));

            }


        }
    }

    /**
     * @Route("/validation", name="interne_finances_payement_validation", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function validationAjaxAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {


            //on récupère les données du formulaire
            $idPayement = $request->request->get('idPayement');
            $action = $request->request->get('action');

            //conversion string to other type
            $idPayement = intval($idPayement); //cast sur int

            //chargement BDD
            $em = $this->getDoctrine()->getManager();

            //chargement du payement
            $payement = $em->getRepository('AppBundle:Payement')->find($idPayement);


            $results = $this->compareWithFactureInBDD($em,array($payement)); //on analise uniquement ce payement
            $result = $results[0];//on prend le premier résultat (et le seul)

            $facture = $result['facture'];
            $statut = $result['statut'];
            $datePayement = $payement->getDatePayement();

            switch($action){
                case 'ignore':

                    echo(0);
                    /*
                     * c'est les cas: not_found ou found_payed
                     */
                    $payement->setState($statut);
                    break;

                case 'validate':
                    $payement->setState($statut);

                    $facture->setStatut('payee');
                    $facture->setDatePayement($datePayement);

                    //validation des créances de la factures
                    foreach($facture->getCreances() as $creance)
                    {
                        $creance->setMontantRecu($creance->getMontantEmis());
                    }

                    //validation des rappels de la facture
                    foreach($facture->getRappels() as $rappel)
                    {
                        $rappel->setMontantRecu($rappel->getMontantEmis());
                    }

                    break;

                case 'repartition':
                    $payement->setState($statut);

                    $facture->setStatut('payee');
                    $facture->setDatePayement($datePayement);
                    $this->repartitionMontantInFacture($request,$facture);


                    break;

                case 'repartition_and_new_facture':
                    $payement->setState($statut);

                    $facture->setStatut('payee');
                    $facture->setDatePayement($datePayement);
                    $this->repartitionMontantInFacture($request,$facture);

                    /*
                     * dans ce cas de figure, on crée des créances supplémentaires
                     * pour compenser le montant exigé
                     */



                    foreach($facture->getCreances() as $creance) {
                        $soldeRestant = $creance->getMontantEmis()-$creance->getMontantRecu();
                        if($soldeRestant > 0) {

                            $owner = $creance->getOwner();


                            $newCreance = null;
                            if($owner->isClass('Membre')) {
                                $newCreance = new CreanceToMembre();
                                $owner->addCreance($newCreance);
                            }
                            if($owner->isClass('Famille')) {
                                $newCreance = new CreanceToFamille();
                                $owner->addCreance($newCreance);
                            }

                            $newCreance->setRemarque($creance->getRemarque().' (Crée en complément de la facture numéro: '.$facture->getId().')');
                            $newCreance->setTitre($creance->getTitre());
                            $newCreance->setMontantEmis($soldeRestant);
                            $today = new \DateTime();
                            $newCreance->setDateCreation($today);

                            $em->persist($newCreance);
                            $em->flush();

                        }
                    }

                break;

            }
            $em->flush();

            return new Response();
        }

        return new Response();
    }

    private function repartitionMontantInFacture($request, $facture)
    {
        $AppBundleFactureRepartitionType = null;
        $serializedForm = $request->request->get('form');
        /**
         * Parse_str va crée le tableau $AppBundleFactureRepartitionType
         */
        parse_str($serializedForm);
        $repartitionArray = $AppBundleFactureRepartitionType;


        //validation des créances de la factures
        $index = 0;
        foreach ($facture->getCreances() as $creance) {
            $creance->setMontantRecu($repartitionArray['creances'][$index]['montantRecu']);
            $index++;
        }

        //validationd des rappels de la facture
        $index = 0;
        foreach ($facture->getRappels() as $rappel) {
            $rappel->setMontantRecu($repartitionArray['rappels'][$index]['montantRecu']);
            $index++;
        }
    }





}