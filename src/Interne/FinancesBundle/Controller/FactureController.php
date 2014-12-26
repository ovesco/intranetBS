<?php

namespace Interne\FinancesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;





class FactureController extends Controller
{


    /**
     * @Route("/facture/delete_ajax", name="interne_fiances_facture_delete_ajax", options={"expose"=true})
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
            return $this->render('InterneFinancesBundle:Externe:interfaceForFamilleOrMembre.html.twig',
                array('ownerEntity' => $facture->getOwner()));
        }
        return new Response();
    }

    /**
     * @Route("/facture/show/{id}", name="interne_fiances_facture_show")
     * @param $id
     * @return Response
     */
    public function showAction($id){

        $em = $this->getDoctrine()->getManager();
        $facture = $em->getRepository('InterneFinancesBundle:Facture')->find($id);
        return $this->render('InterneFinancesBundle:Facture:show.html.twig',
            array('facture' => $facture));

    }




}