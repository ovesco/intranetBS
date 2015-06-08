<?php

namespace Interne\FinancesBundle\Controller;

/* Routing */
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;


/* Entity */
use Interne\FinancesBundle\Entity\CreanceToFamille;
use Interne\FinancesBundle\Entity\CreanceToMembre;
use Interne\FinancesBundle\Entity\FactureRepository;
use Interne\FinancesBundle\Entity\Facture;
use Interne\FinancesBundle\Entity\Payement;

/* Form */
use Interne\FinancesBundle\Form\FactureRepartitionType;
use Interne\FinancesBundle\Form\PayementSearchType;

/* Other */
use Interne\FinancesBundle\SearchClass\PayementSearch;
use Interne\FinancesBundle\SearchRepository\PayementRepository;


/**
 * Class PayementController
 * @package Interne\FinancesBundle\Controller
 * @Route("/payement")
 */
class PayementController extends Controller
{

    /**
     * @Route("/search", name="interne_fiances_payement_search", options={"expose"=true})
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
     * @Route("/show/{payement}", name="interne_fiances_payement_show", options={"expose"=true})
     * @param Payement $payement
     * @ParamConverter("payement", class="InterneFinancesBundle:Payement")
     * @Template("InterneFinancesBundle:Payement:showModal.html.twig")
     * @return Response
     */
    public function showAction(Payement $payement){
        return array('payement'=>$payement);
    }














    /**
     * @Route("/", name="interne_fiances_payement")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('InterneFinancesBundle:Payement:page_payement.html.twig');
    }

    /**
     * @Route("/waiting_liste", name="interne_fiances_payement_waiting_liste", options={"expose"=true})
     * @return Response
     */
    public function getWaitingListeAction()
    {
        $em = $this->getDoctrine()->getManager();

        $payements = $em->getRepository('InterneFinancesBundle:Payement')->findByState('waiting');

        $results =  $this->compareWithFactureInBDD($em,$payements);

        return $this->render('InterneFinancesBundle:Payement:waitingListe.html.twig',array('waitingListe'=>$results));
    }


    /**
     * @Route("/add_manualy", name="interne_fiances_payement_add_manualy", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function addManualyAjaxAction(Request $request)
    {

        $request = $this->getRequest();

        if($request->isXmlHttpRequest())
        {
            $idFacture = $request->request->get('idFacture');
            $montantRecu = $request->request->get('montantRecu');

            $idFacture = (int)$idFacture; //cast sur int

            $payement = new Payement($idFacture,$montantRecu,new \Datetime,'waiting');

            $em = $this->getDoctrine()->getManager();
            $em->persist($payement);
            $em->flush();

            return new Response('success');

        }
        return new Response('error');
    }

    /*
     * Charge le fichier V11 qui contient une liste de payement payée sur le compte
     * BVR. On extrait les infos du fichier
     */
    /**
     * @route("/upload_file", name="interne_fiances_payement_upload_file", options={"expose"=true})
     *
     * @return Response
     */
    public function uploadFileAjaxAction()
    {
        $request = $this->getRequest();

        if($request->isXmlHttpRequest())
        {
            $file = $request->files->get('file');

            $array = $this->extractFacturesInFile($file);

            $payementsInFile = $array['payements'];
            $infos = $array['infos'];

            $em = $this->getDoctrine()->getManager();
            foreach($payementsInFile as $payement)
            {
                $em->persist($payement);
            }
            $em->flush();

            return new Response();
        }
    }








    /**
     * @route("/repartition_payement", name="interne_fiances_payement_repartition_ajax", options={"expose"=true})
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

    private function extractFacturesInFile($file)
    {
        /*
         * extraction du contenu du fichier.
         */
        $fileString = file($file);
        $nbLine = count($fileString);

        /*
         * création des conteneurs de résultats de la fonction.
         */
        $facturesInFile = new ArrayCollection();
        $infos = array();

        /*
         * analyse ligne par ligne du fichier-
         */
        for ($i = 0; $i < $nbLine; $i++) {

            $line = $fileString[$i];
            $infos = array();
            $infos['rejetsBvr'] = 0;

            if (substr($line, 0, 1) != 9) {
                //extraction des infos de la ligne
                $numRef = substr($line, 12, 26);
                $montantRecu = substr($line, 39, 10);
                $datePayement = substr($line, 71, 6);
                $rejetBVR = substr($line, 86, 1);

                /*
                 * enregistre le nombre de facture qui ont
                 * été rejetée et rentrée à la main par
                 * la poste.
                 */
                if($rejetBVR)
                {
                    $infos['rejetsBvr'] =$infos['rejetsBvr']+1;
                }

                //reformatage des chaines de caractère
                $numRef = (integer)ltrim($numRef,0);
                $montantRecu = (float)(ltrim($montantRecu,0)/100);
                $date_payement_annee = '20'. substr($datePayement,0,2);
                $date_payement_mois = substr($datePayement,2,2);
                $date_payement_jour = substr($datePayement,4,2);
                $datePayement = new \DateTime();
                $datePayement->setDate((int)$date_payement_annee,(int)$date_payement_mois,(int)$date_payement_jour);

                /*
                 * création du payement extraite de la ligne
                 */
                $payement = new Payement($numRef,$montantRecu,$datePayement,'waiting');

                $payementsInFile[] = $payement;
            }
            else
            {
                /*
                 * Infos sur les factures présente dans ce fichier.
                 * Elle sont stoquées sur la ligne qui commence
                 * par un 9.
                 */
                $infos['genreTransaction'] = substr($line, 0, 3);
                $infos['montantTotal'] = ltrim(substr($line, 39, 12),0);
                $infos['nbTransactions'] = ltrim(substr($line, 51, 12),0);
                $infos['dateDisquette'] = substr($line, 63, 6);
                $infos['taxes'] = substr($line, 69, 9);

            }
        }

        return array('payements' => $payementsInFile, 'infos' => $infos);
    }

    private function compareWithFactureInBDD($em,$payements)
    {
        $factureRepository = $em->getRepository('InterneFinancesBundle:Facture');


        $results = array();

        foreach($payements as $payement)
        {


            $factureFound = $factureRepository->find($payement->getIdFacture());

            $validationStatut = null;

            if($factureFound != Null)
            {
                if($factureFound->getStatut() == 'ouverte')
                {
                    $montantTotalEmis = $factureFound->getMontantEmis();
                    $montantRecu = $payement->getMontantRecu();

                    if($montantTotalEmis == $montantRecu)
                    {
                        $validationStatut = 'found_valid';
                    }
                    elseif($montantTotalEmis > $montantRecu)
                    {
                        $validationStatut = 'found_lower';
                    }
                    elseif($montantTotalEmis < $montantRecu)
                    {
                        $validationStatut = 'found_upper';
                    }
                }
                else
                {
                    /*
                     * la facture a déjà été payée
                     */
                    $validationStatut = 'found_payed';
                }


            }
            else
            {
                $validationStatut = 'not_found';
            }

            $results[] = array(
                'id'=>$payement->getId(),
                'payement' => $payement,
                'facture' => $factureFound,
                'statut' => $validationStatut
            );
        }



        return $results;
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