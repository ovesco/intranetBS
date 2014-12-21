<?php

namespace Interne\FactureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Interne\FactureBundle\Entity\Rappel;
use Interne\FactureBundle\Entity\Facture;

class ListeController extends Controller
{

    public function addRappelsAjaxAction()
    {
        $request = $this->getRequest();



        if($request->isXmlHttpRequest()) {

            $listeFactureId = $request->request->get('listeFactureId');

            $frais = $request->request->get('frais_rappel');

            $em = $this->getDoctrine()->getManager();

            $factures = array();
            foreach($listeFactureId as $id)
            {
                $facture = $em->getRepository('InterneFactureBundle:Facture')->find($id);
                $rappel = new Rappel();
                $rappel->setFrais($frais);
                $rappel->setDate(new \DateTime());
                $facture->addRappel($rappel);
                $em->persist($rappel);
                //construit le tableau
                $factures[] = $facture;

            }
            $em->flush();

            return $this->render('InterneFactureBundle:Liste:tableLineInput.html.twig',
                array('factures' => $factures));
        }

        return new Response();

    }

    public function payedAjaxAction()
    {
        $request = $this->getRequest();

        if($request->isXmlHttpRequest()) {

            $listeFactureId = $request->request->get('listeFactureId');
            $em = $this->getDoctrine()->getManager();

            $factures = array();
            foreach($listeFactureId as $id)
            {
                $facture = $em->getRepository('InterneFactureBundle:Facture')->find($id);
                $facture->setStatut('payee');
                $facture->setDatePayement(new \DateTime());
                //construit le tableau
                $factures[] = $facture;

            }
            $em->flush();

            return $this->render('InterneFactureBundle:Liste:tableLineInput.html.twig',
                array('factures' => $factures));
        }

        return new Response();

    }

    public function deleteAjaxAction()
    {
        $request = $this->getRequest();

        if($request->isXmlHttpRequest()) {

            $listeFactureId = $request->request->get('listeFactureId');
            $em = $this->getDoctrine()->getManager();

            foreach($listeFactureId as $id)
            {

                $facture = $em->getRepository('InterneFactureBundle:Facture')->find($id);

                $em->remove($facture);

            }
            $em->flush();

            return new Response();
        }

        return new Response();

    }


}