<?php

namespace Interne\FinancesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Interne\FinancesBundle\Entity\FactureToMembre;
use Interne\FinancesBundle\Entity\FactureToFamille;
use Interne\FinancesBundle\Entity\Facture;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


/**
 * Class FactureController
 * @package Interne\FinancesBundle\Controller
 *
 * @Route("/facture")
 */
class FactureController extends Controller
{


    /**
     * @Route("/delete_ajax", name="interne_fiances_facture_delete_ajax", options={"expose"=true})
     *
     * @return Response
     */
    public function deleteAjaxAction()
    {
        $request = $this->getRequest();

        if($request->isXmlHttpRequest()) {

            $id = $request->request->get('idFacture');
            $em = $this->getDoctrine()->getManager();
            $facture = $em->getRepository('InterneFinancesBundle:Facture')->find($id);

            //on verifie que la facture existe bien, si c'est pas le cas, on affiche l'index
            if ($facture != Null) {
                $em->remove($facture);
                $em->flush();
            }
            return new Response();
        }
        return new Response();
    }



    /**
     * @Route("/show_ajax", name="interne_fiances_facture_show_ajax", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function showAjaxAction(Request $request){

        if($request->isXmlHttpRequest()) {

            $id = $request->request->get('idFacture');

            $em = $this->getDoctrine()->getManager();
            $facture = $em->getRepository('InterneFinancesBundle:Facture')->find($id);
            return $this->render('InterneFinancesBundle:Facture:modalContentShow.html.twig',
                array('facture' => $facture));
        }

        return new Response();
    }




    /*
     * Cette methode permet de facturer une liste de cérance
     * depuis plusieur page différente.
     */
    /**
     * @Route("/create_ajax", name="interne_fiances_facture_create_ajax", options={"expose"=true})
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
     * @Route("/envoi", name="interne_fiances_facture_envoi_ajax", options={"expose"=true})
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

    /**
     * @param Facture $facture
     * @Route("/print/{facture}", name="interne_fiances_facture_print", options={"expose"=true})
     * @return Response
     * @ParamConverter("facture", class="InterneFinancesBundle:Facture")
     */
    public function printAction(Facture $facture)
    {

        $printer = $this->get('finances_printer');
        $pdf = $printer->factureToPdf($facture);

        /*
         * Ajout de l'adresse
         */
        $adresse = $facture->getOwner()->getAdresseExpedition();
        $pdf->addAdresseEnvoi($adresse);

        return $pdf->Output('Facture N°'.$facture->getId().'.this->pdf','I');

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