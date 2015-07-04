<?php

namespace Interne\FinancesBundle\Controller;

/* Routing */
use Doctrine\ORM\EntityManager;
use Interne\FinancesBundle\Form\PayementAddType;
use Interne\FinancesBundle\Form\PayementValidationType;
use Interne\FinancesBundle\SearchRepository\FactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/* Entity */
use Interne\FinancesBundle\Entity\CreanceToFamille;
use Interne\FinancesBundle\Entity\CreanceToMembre;
use Interne\FinancesBundle\Entity\Facture;
use Interne\FinancesBundle\Entity\Payement;

/* Form */
use Interne\FinancesBundle\Form\FactureRepartitionType;
use Interne\FinancesBundle\Form\PayementSearchType;
use Interne\FinancesBundle\Form\PayementAddMultipleType;
use Interne\FinancesBundle\Form\PayementUploadFileType;

/* Other */
use Interne\FinancesBundle\SearchClass\PayementSearch;
use Interne\FinancesBundle\SearchRepository\PayementRepository;

/* Services */
use Interne\FinancesBundle\Utils\PayementFileParser;


/**
 * Class PayementController
 * @package Interne\FinancesBundle\Controller
 * @Route("/payement")
 */
class PayementController extends Controller
{

    /**
     * Page for searching payement
     *
     * @Route("/search", name="interne_finances_payement_search", options={"expose"=true})
     * @param Request $request
     * @return Response
     * @Template("InterneFinancesBundle:Payement:page_recherche.html.twig")
     */
    public function searchAction(Request $request){

        $payementSearch = new PayementSearch();

        $searchForm = $this->createForm(new PayementSearchType,$payementSearch);

        $results = array();

        $searchForm->handleRequest($request);

        if ($searchForm->isValid()) {

            $payementSearch = $searchForm->getData();

            $elasticaManager = $this->container->get('fos_elastica.manager');

            /** @var PayementRepository $repository */
            $repository = $elasticaManager->getRepository('InterneFinancesBundle:Payement');

            $results = $repository->search($payementSearch);

        }

        return array('searchForm'=>$searchForm->createView(),'payements'=>$results);
    }

    /**
     * Return modal of the payement
     *
     * @Route("/show/{payement}", name="interne_finances_payement_show", options={"expose"=true})
     * @param Payement $payement
     * @ParamConverter("payement", class="InterneFinancesBundle:Payement")
     * @Template("InterneFinancesBundle:Payement:showModal.html.twig")
     * @return Response
     */
    public function showAction(Payement $payement){
        return array('payement'=>$payement);
    }


    /**
     * Page for adding new payement (manualy or by uploading file)
     *
     * @Route("/add", name="interne_finances_payement_add", options={"expose"=true})
     * @param Request $request
     * @Template("InterneFinancesBundle:Payement:page_saisie.html.twig")
     * @return Response
     */
    public function addAction(Request $request){

        $form = $this->processAddForm($request);

        $upload = $this->processUploadForm($request);

        return array('formMultiple'=>$form->createView(),'formUpload'=>$upload->createView());
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\Form\Form
     *
     * Cette fonction fournit le forumlaire d'ajout multiple de payement pour la page "add"
     *
     */
    private function processAddForm(Request $request){

        $form  = $this->createForm(new PayementAddMultipleType());
        $form->get('multiple_payement')->setData(array(new Payement()));
        $form->add('Ajouter','submit');
        $form->add('Anuller','reset');

        $form->handleRequest($request);

        if($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $payements = $form->get('multiple_payement')->getData();

            $sucessString = '';
            $errorString = '';
            /** @var Payement $payement */
            foreach($payements as $payement)
            {

                /*
                 * On controle que le payement soit bien valide
                 */
                if(($payement->getIdFacture() != null) && ($payement->getMontantRecu() != null))
                {
                    $payement->setDate(new \DateTime());
                    $payement->setValidated(false);
                    $payement->checkState($em);

                    $em->persist($payement);

                    $sucessString = $sucessString.'Payement '.$payement->getIdFacture().' avec '.$payement->getMontantRecu().'CHF enregisté!'."\r\n";
                }
                elseif(($payement->getIdFacture() != null) || ($payement->getMontantRecu() != null))
                {
                    /*
                     * Envoi d'information sur l'erreur via flashbag
                     */
                    $errorString = $errorString.'Payement '.$payement->getIdFacture().' avec '.$payement->getMontantRecu().'CHF non valide!'."\r\n";
                }

            }
            $em->flush();
            /*
             * Send info via flashbag
             */
            $this->get('session')->getFlashBag()->add('success',$sucessString);
            $this->get('session')->getFlashBag()->add('error',$errorString);

        }
        return $form;
    }

    private function processUploadForm(Request $request){

        $upload = $this->createForm(new PayementUploadFileType());
        $upload->add('Charger','submit');

        if ($request->isMethod('POST')) {

            $upload->handleRequest($request);

            if($upload->isValid()){
                /** @var UploadedFile $file */
                $file = $upload['file']->getData();

                /** @var PayementFileParser $payementParser */
                $payementParser = $this->get('payement_file_parser');
                $payementParser->setFile($file);
                $payementParser->extract();
                /** @var ArrayCollection $payements */
                $payements = $payementParser->getPayements();

                $em = $this->getDoctrine()->getManager();
                foreach($payements as $payement)
                {
                    $payement->checkState($em);
                    $em->persist($payement);
                }
                $em->flush();

                $this->get('session')->getFlashBag()->add('success','Fichier valide avec '.$payements->count().' payements ajoutés.');
            }
            else
            {
                $this->get('session')->getFlashBag()->add('error','Fichier non valide');
            }

        }



        return $upload;

    }

    /**
     * Page for validation of payements
     *
     * @Route("/validation", name="interne_finances_payement_validation_list", options={"expose"=true})
     * @param Request $request
     * @Template("InterneFinancesBundle:Payement:page_validation.html.twig")
     * @return Response
     */
    public function validationAction(Request $request){


        $em = $this->getDoctrine()->getManager();

        /*
         * todo faire ceci avec elasitca (pas nécaissaire dans l'imédia)
        */

        $payements = $em->getRepository('InterneFinancesBundle:Payement')->findBy(array('validated'=>false),null,10);

        return array('payements'=>$payements);


    }

    /**
     * Validation form
     *
     * @Route("/validation_form/{payement}", name="interne_finances_payement_validation_form", options={"expose"=true})
     * @param Request $request
     * @param Payement $payement
     * @ParamConverter("payement", class="InterneFinancesBundle:Payement")
     * @Template("InterneFinancesBundle:Payement:validationForm.html.twig")
     * @return Response
     */
    public function validationFormAction(Request $request,Payement $payement)
    {
        $validationForm  = $this->createForm(new PayementValidationType(),$payement);

        if($request->isXmlHttpRequest()){

            $validationForm->handleRequest($request);

            if($validationForm->isValid()) {

                $em = $this->getDoctrine()->getManager();

                switch($payement->getState()){
                    case Payement::FOUND_LOWER:
                    case Payement::FOUND_VALID:
                    case Payement::FOUND_UPPER:
                        $payement->getFacture()->setStatut(Facture::PAYEE);
                        break;
                }

                /*
                 * todo verifier le montant avant la validation.
                 */

                $payement->setValidated(true);
                $em->persist($payement);
                $em->flush();

                $success = new Response();
                $success->setStatusCode(200);//ok
                return $success;

            }
            else{
                $error = new Response();
                $error->setStatusCode(400);//bad request
                return $error;
            }

        }



        return array('payement'=>$payement,'form'=>$validationForm->createView());
    }
































































    /**
     * @route("/repartition_payement", name="interne_finances_payement_repartition_ajax", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function getPayementRepartitionFormAjaxAction(Request $request){

        if($request->isXmlHttpRequest()) {

            $idPayement = $request->request->get('idPayement');

            $em = $this->getDoctrine()->getManager();
            $payement = $em->getRepository('InterneFinancesBundle:Payement')->find($idPayement);


            if($payement != null){

                $facture = $em->getRepository('InterneFinancesBundle:Facture')->find($payement->getIdFacture());


                $repartitionForm = $this->createForm(new FactureRepartitionType(),$facture);

                return $this->render('InterneFinancesBundle:Payement:modalRepartitionForm.html.twig',
                    array('form'=>$repartitionForm->createView(),'payement'=>$payement,'facture'=>$facture));

            }


        }
    }




    private function repartitionMontantInFacture($request,$facture)
    {
        $InterneFinancesBundleFactureRepartitionType = null;
        $serializedForm = $request->request->get('form');
        /**
         * Parse_str va crée le tableau $InterneFinancesBundleFactureRepartitionType
         */
        parse_str($serializedForm);
        $repartitionArray = $InterneFinancesBundleFactureRepartitionType;


        //validation des créances de la factures
        $index = 0;
        foreach($facture->getCreances() as $creance)
        {
            $creance->setMontantRecu($repartitionArray['creances'][$index]['montantRecu']);
            $index++;
        }

        //validationd des rappels de la facture
        $index = 0;
        foreach($facture->getRappels() as $rappel)
        {
            $rappel->setMontantRecu($repartitionArray['rappels'][$index]['montantRecu']);
            $index++;
        }
    }

    /**
     * @route("/validation", name="interne_finances_payement_validation", options={"expose"=true})
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
            $payement = $em->getRepository('InterneFinancesBundle:Payement')->find($idPayement);


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





}