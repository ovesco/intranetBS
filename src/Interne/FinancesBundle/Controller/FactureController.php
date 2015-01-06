<?php

namespace Interne\FinancesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Interne\FinancesBundle\Entity\Facture;


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
     *
     * @return Response
     */
    public function showAjaxAction(){

        $request = $this->getRequest();

        if($request->isXmlHttpRequest()) {

            $id = $request->request->get('idFacture');
            $fromPage = $request->request->get('fromPage');

            $em = $this->getDoctrine()->getManager();
            $facture = $em->getRepository('InterneFinancesBundle:Facture')->find($id);
            return $this->render('InterneFinancesBundle:Facture:modalContentShow.html.twig',
                array('facture' => $facture,
                    'fromPage'=>$fromPage));
        }

        return new Response();
    }




    /*
     * Cette methode permet de facturer une liste de cérance
     * depuis plusieur page différente.
     */
    /**
     * @Route("/create_ajax", name="interne_fiances_facture_create_ajax", options={"expose"=true})
     *
     * @return Response
     */
    public function facturationAjaxAction()
    {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {

            /*
             * On récupère les données
             */
            $listeIdCreance = $request->request->get('listeCreance');

            //cération des nouvelles factures
            $this->createFacture($listeIdCreance);

            return new Response();


        }
        return new Response();
    }

    /**
     * @Route("/envoi", name="interne_fiances_facture_envoi_ajax", options={"expose"=true})
     *
     * @return Response
     */
    public function factureEnvoiAjaxAction()
    {
        $request = $this->getRequest();

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
            $pdf = $this->get('Pdf'); //call service
            $printer = new PrintController();
            $pdf = $printer->factureToPdf($em,$facture,$pdf);

            $ownerId = $facture->getOwner()->getId();
            $ownerClass = $facture->getOwner()->getClass();

            $listeEnvoi = $this->get('listeEnvoi');
            $listeEnvoi->addEnvoi($ownerId,$ownerClass,$pdf,'Facture N°'.$idFacture);
            $listeEnvoi->save();

            return new Response();


        }
        return new Response();

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
                $famille = $creance->getFamille();
                $membre = $creance->getMembre();

                $cibleFacturation = '';

                if ($famille != null) {
                    /*
                     * la créance appartien à une famille
                     */
                    $cibleFacturation = 'Famille';
                } elseif ($membre != null) {
                    /*
                     * la cérance appartient à un membre
                     */
                    $cibleFacturation = $membre->getEnvoiFacture(); //retourne soit 'Famille' soit 'Membre'
                    if ($cibleFacturation == 'Famille') {
                        //on récupère la famille du membre
                        $famille = $membre->getFamille();
                    }
                }

                /*
                 * Creation de la nouvelle facture
                 */
                $facture = new Facture();
                $facture->setDateCreation(new \DateTime());
                $facture->setStatut('ouverte');


                /*
                 * On procède de manière différente selon
                 *  la cible de facturation.
                 */

                switch ($cibleFacturation) {

                    case 'Membre':

                        foreach ($membre->getCreances() as $linkedCreance) {
                            /*
                             * On récupère toute les créances du membre
                             * qui ne sont pas encore facturée
                             * !!! Et qui apparitennent à la liste !!!
                             */
                            if ((!$linkedCreance->isFactured()) && in_array($linkedCreance->getId(), $listeIdCreance)) {
                                $facture->addCreance($linkedCreance);
                            }
                        }
                        $membre->addFacture($facture);

                        break;

                    case 'Famille':

                        foreach ($famille->getCreances() as $linkedCreance) {
                            /*
                             * On récupère toute les créances de la famille
                             * qui ne sont pas encore facturée
                             * !!! Et qui apparitennent à la liste !!!
                             */
                            if ((!$linkedCreance->isFactured()) && in_array($linkedCreance->getId(), $listeIdCreance)) {
                                $facture->addCreance($linkedCreance);
                            }
                        }

                        foreach ($famille->getMembres() as $membreOfFamille) {
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

                        $famille->addFacture($facture);
                        break;

                }

                $em->persist($facture);
                $em->flush();
            }
        }
    }




}