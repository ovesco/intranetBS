<?php

namespace Interne\FinancesBundle\Controller;

/* Symfony */
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/* Entity */
use Interne\FinancesBundle\Entity\FactureToMembre;
use Interne\FinancesBundle\Entity\FactureToFamille;
use Interne\FinancesBundle\Entity\Facture;

/* Form */
use Interne\FinancesBundle\Form\FactureSearchType;

/* Elastica repository */
use Interne\FinancesBundle\SearchRepository\FactureToFamilleRepository;
use Interne\FinancesBundle\SearchRepository\FactureToMembreRepository;
use Interne\FinancesBundle\SearchClass\FactureSearch;

/* routing */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


/**
 * Class FactureController
 * @package Interne\FinancesBundle\Controller
 *
 * @Route("/facture")
 */
class FactureController extends Controller
{

    /**
     * @Route("/search", name="interne_finances_facture_search", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function searchAction(Request $request){


        $factureSearch = new FactureSearch();

        $searchForm = $this->createForm(new FactureSearchType,$factureSearch);

        $results = array();

        $searchForm->handleRequest($request);

        if ($searchForm->isValid()) {

            $factureSearch = $searchForm->getData();


            $elasticaManager = $this->container->get('fos_elastica.manager');

            /** @var FactureToMembreRepository $repository */
            $repository = $elasticaManager->getRepository('InterneFinancesBundle:FactureToMembre');

            $resultsFactureToMembre = $repository->search($factureSearch);

            /** @var FactureToFamilleRepository $repository */
            $repository = $elasticaManager->getRepository('InterneFinancesBundle:FactureToFamille');

            $resultsFactureToFamille = $repository->search($factureSearch);

            $results = array_merge($resultsFactureToMembre,$resultsFactureToFamille);

        }


        return $this->render('InterneFinancesBundle:Facture:page_recherche.html.twig',
            array('searchForm'=>$searchForm->createView(),'factures'=>$results));

    }


    /**
     * @Route("/show/{facture}", name="interne_finances_facture_show", options={"expose"=true})
     * @param Facture $facture
     * @ParamConverter("facture", class="InterneFinancesBundle:Facture")
     * @param Request $request
     * @return Response
     * @Template("InterneFinancesBundle:Facture:modalContentShow.html.twig")
     */
    public function showAction(Request $request,Facture $facture){

        return  array('facture' => $facture);
    }


    /**
     * @Route("/delete/{facture}", name="interne_finances_facture_delete", options={"expose"=true})
     * @param Facture $facture
     * @ParamConverter("facture", class="InterneFinancesBundle:Facture")
     * @param Request $request
     * @return Response
     */
    public function deleteAction(Request $request,Facture $facture)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($facture);
        $em->flush();
        $response = new Response();
        return $response->setStatusCode(200);//OK
    }

    /**
     * Create a PDF and send it to the client browser
     *
     * @param Facture $facture
     * @Route("/print/{facture}", name="interne_finances_facture_print", options={"expose"=true})
     * @return Response
     * @ParamConverter("facture", class="InterneFinancesBundle:Facture")
     */
    public function printAction(Facture $facture)
    {

        $printer = $this->get('facture_printer');
        $pdf = $printer->factureToPdf($facture);

        /*
         * Ajout de l'adresse
         */
        $adresse = $facture->getOwner()->getAdresseExpedition();
        $pdf->addAdresseEnvoi($adresse);

        //return $pdf->Output('Facture N°'.$facture->getId().'.this->pdf','I');


        $filePath = $this->get('kernel')->getCacheDir().'/temp_pdf/';
        $fileName = 'facture_'.$facture->getId().'.pdf';

        $fs = new Filesystem();

        if(!$fs->exists($filePath))
        {
            $fs->mkdir($filePath);
        }


        /*
         * Save the PDF in cache dir
         */
        $pdf->Output($filePath.$fileName,'F');
        //todo le cache va grandir à chache facture imprimée...resoudre ceci

        return new BinaryFileResponse($filePath.$fileName);
    }






    /*
     * TODO CODE CI DESSOUS A REVOIR
     */



















    /*
     * Cette methode permet de facturer une liste de cérance
     * depuis plusieur page différente.
     */
    /**
     * @Route("/create_ajax", name="interne_finances_facture_create_ajax", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function facturationAjaxAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {

            /*
             * On récupère les données
             */
            $listeIdCreance = $request->request->get('listeCreance');

            //cération des nouvelles factures
            $this->createFacture($listeIdCreance);

            return new Response('success');


        }
        return new Response('error');
    }

    /**
     * @Route("/envoi", name="interne_finances_facture_envoi_ajax", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function factureEnvoiAjaxAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {

            /*
             * On récupère les données
             */
            $idFacture = $request->request->get('idFacture');
            $em = $this->getDoctrine()->getManager();
            $facture = $em->getRepository('InterneFinancesBundle:Facture')->find($idFacture);


            /*
             * Creation du PDF
             */
            $printer = $this->get('finances_printer');
            $pdf = $printer->factureToPdf($facture);

            $ownerId = $facture->getOwner()->getId();
            $ownerClass = $facture->getOwner()->getClass();

            $listeEnvoi = $this->get('listeEnvoi');
            $listeEnvoi->addEnvoiWithPdf($ownerId,$ownerClass,$pdf,'Facture N°'.$idFacture);




            return new Response('success');


        }
        return new Response('error');

    }






    /*
     * Creation de factures avec une liste de créances (Id).
     *
     * Remarque: cette fonction va grouper les factures par unité de
     * facturation. Cela marche uniquement pour les factures
     * présente dans la liste d'IDs
     */
    /**
     * @param Array $listeIdCreance
     */
    private function createFacture($listeIdCreance)
    {


        /*
         * On load la base de donnée
         */
        $em = $this->getDoctrine()->getManager();
        $creanceRepo = $em->getRepository('InterneFinancesBundle:Creance');

        /*
         * On va mettre les créance de la liste dans des facture
         */

        foreach ($listeIdCreance as $creanceId) {
            $creance = $creanceRepo->find($creanceId);
            /*
             * La fonction va parcourire la liste des creances mais il se peut que
             * la facturation aie été déjà faite dans une itération précédente.
             * On va donc s'assurer que la créance n'est pas encore liée à une
             * facture.
             */
            if ($creance->getFacture() == null) {
                /*
                 * On commence par regarder si la créance
                 * appartien à un membre ou une famille.
                 * Ainsi que déterminer la cible de facturation
                 */


                $cibleFacturation = null;

                if($creance->getOwner()->isClass('Famille'))
                {
                    /*
                     * la créance appartien à une famille
                     */
                    $cibleFacturation = 'Famille';
                }
                elseif($creance->getOwner()->isClass('Membre'))
                {
                    /*
                     * la cérance appartient à un membre
                     */
                    $cibleFacturation = $creance->getOwner()->getEnvoiFacture(); //retourne soit 'Famille' soit 'Membre'

                }




                /*
                 * On procède de manière différente selon
                 *  la cible de facturation.
                 */

                switch ($cibleFacturation) {

                    case 'Membre':

                        /*
                         * Creation de la nouvelle facture
                         */
                        $facture = new FactureToMembre();
                        $facture->setDateCreation(new \DateTime());

                        foreach ($creance->getOwner()->getCreances() as $linkedCreance) {
                            /*
                             * On récupère toute les créances du membre
                             * qui ne sont pas encore facturée
                             * !!! Et qui apparitennent à la liste !!!
                             */
                            if ((!$linkedCreance->isFactured()) && in_array($linkedCreance->getId(), $listeIdCreance)) {
                                $facture->addCreance($linkedCreance);
                            }
                        }
                        $creance->getOwner()->addFacture($facture);
                        $em->persist($facture);
                        break;

                    case 'Famille':

                        /*
                         * Creation de la nouvelle facture
                         */
                        $facture = new FactureToFamille();
                        $facture->setDateCreation(new \DateTime());

                        foreach ($creance->getOwner()->getCreances() as $linkedCreance) {
                            /*
                             * On récupère toute les créances de la famille
                             * qui ne sont pas encore facturée
                             * !!! Et qui apparitennent à la liste !!!
                             */
                            if ((!$linkedCreance->isFactured()) && in_array($linkedCreance->getId(), $listeIdCreance)) {
                                $facture->addCreance($linkedCreance);
                            }
                        }

                        foreach ($creance->getOwner()->getMembres() as $membreOfFamille) {
                            /*
                             * On recherche des créances chez les
                             * membre de la famille qui envoie
                             * leurs facture à la famille
                             */
                            if ($membreOfFamille->getEnvoiFacture() == 'Famille') {
                                foreach ($membreOfFamille->getCreances() as $linkedCreance) {
                                    /*
                                     * On récupère toute les créances du membre
                                     * qui ne sont pas encore facturée
                                     * !!! Et qui apparitennent à la liste !!!
                                     */
                                    if ((!$linkedCreance->isFactured()) && in_array($linkedCreance->getId(), $listeIdCreance)) {
                                        $facture->addCreance($linkedCreance);
                                    }
                                }
                            }
                        }

                        $creance->getOwner()->addFacture($facture);
                        $em->persist($facture);
                        break;

                }


                $em->flush();
            }
        }
    }






}