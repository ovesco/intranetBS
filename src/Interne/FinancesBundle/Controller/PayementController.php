<?php

namespace Interne\FinancesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Interne\FinancesBundle\Entity\Payement;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use Interne\FinancesBundle\Form\PayementSearchType;

/**
 * Class PayementController
 * @package Interne\FinancesBundle\Controller
 * @Route("/payement")
 */
class PayementController extends Controller
{

    /**
     * @Route("/", name="interne_fiances_payement")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $payementSearchForm  = $this->createForm(new PayementSearchType);

        return $this->render('InterneFinancesBundle:Payement:payement.html.twig',
            array('searchForm'=> $payementSearchForm->createView() ));
    }


    /**
     * @Route("/add_manualy", name="interne_fiances_payement_add_manualy", options={"expose"=true})
     *
     *
     */
    public function addManualyAjaxAction()
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



            return $this->render('InterneFinancesBundle:Payement:tableLine.html.twig',
                array('payements'=> array($payement) ));
        }

        return new Response();
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

            return $this->render('InterneFinancesBundle:Payement:tableLine.html.twig',array('payements'=>$payementsInFile));
        }
    }


    /**
     * @route("/search", name="interne_fiances_payement_search_ajax", options={"expose"=true})
     *
     * @return Response
     */
    public function searchAjaxAction()
    {
        $request = $this->getRequest();

        if($request->isXmlHttpRequest()) {

            $payement = new Payement(null,null,null,null);

            $payementSearchForm = $this->createForm(new PayementSearchType,$payement);

            if ($request->request->has('InterneFinancesBundlePayementSearchType')) {

                $payementSearchForm->submit($request);
                $payement = $payementSearchForm->getData();


                $searchParameters = array(
                    'montantRecuMaximum' => $payementSearchForm->get('montantRecuMaximum')->getData(),
                    'montantRecuMinimum' => $payementSearchForm->get('montantRecuMinimum')->getData(),
                    'datePayementMaximum' => $payementSearchForm->get('datePayementMaximum')->getData(),
                    'datePayementMinimum' => $payementSearchForm->get('datePayementMinimum')->getData());



                $em = $this->getDoctrine()->getManager();
                $payements = $em->getRepository('InterneFinancesBundle:Payement')->findBySearch($payement,$searchParameters);

                return $this->render('InterneFinancesBundle:Payement:tableLine.html.twig',array('payements'=>$payements));
            }
        }
        return new Response();
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





}