<?php

namespace Interne\FactureBundle\Controller;

use Interne\FactureBundle\Entity\Creance;
use Interne\FactureBundle\Entity\Facture;
use Interne\FactureBundle\Entity\Rappel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Interne\FactureBundle\Form\FactureType;
use Symfony\Component\Validator\Constraints\Null;
use Interne\FactureBundle\Form\FactureSearchType;
use Doctrine\Common\Collections\ArrayCollection;

class FactureController extends Controller
{

    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $facture = $em->getRepository('InterneFactureBundle:Facture')->find($id);

        //on verifie que la facture existe bien, si c'est pas le cas, on affiche l'index
        if($facture == Null)
        {
            return $this->render('InterneFactureBundle:Default:index.html.twig');
        }


        return $this->render('InterneFactureBundle:Facture:show.html.twig',
            array('facture' => $facture));

    }



    public function updateAction($id)
    {
        $request = $this->get('request');

        $em = $this->getDoctrine()->getManager();
        $facture = $em->getRepository('InterneFactureBundle:Facture')->find($id);

        $factureForm  = $this->createForm(new FactureType, $facture);

        if ($request->isMethod('POST'))
        {
            $factureForm->submit($request);

            if ($factureForm->isValid()) {


                $em->flush();

                return $this->render('InterneFactureBundle:Default:index.html.twig');
            }
        }

        return $this->render('InterneFactureBundle:Facture:update.html.twig', array(
            'factureForm' => $factureForm->createView()
        ));
    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $facture = $em->getRepository('InterneFactureBundle:Facture')->find($id);

        //on verifie que la facture existe bien, si c'est pas le cas, on affiche l'index
        if($facture != Null)
        {
            $em->remove($facture);
            $em->flush();
        }
        return $this->render('InterneFactureBundle:Default:index.html.twig');
    }

    public function deleteAjaxAction()
    {
        $request = $this->getRequest();

        if($request->isXmlHttpRequest()) {

            $id = $request->request->get('idFacture');
            $em = $this->getDoctrine()->getManager();
            $facture = $em->getRepository('InterneFactureBundle:Facture')->find($id);

            //on verifie que la facture existe bien, si c'est pas le cas, on affiche l'index
            if ($facture != Null) {
                $em->remove($facture);
                $em->flush();
            }
            return $this->render('InterneFactureBundle:viewForFichierBundle:interfaceForFamilleOrMembre.html.twig',
                array('ownerEntity' => $facture->getOwner()));
        }
        return new Response();
    }




}