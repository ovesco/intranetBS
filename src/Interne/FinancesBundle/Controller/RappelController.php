<?php

namespace Interne\FinancesBundle\Controller;

use Interne\FinancesBundle\Entity\Rappel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Interne\FinancesBundle\Form\RappelType;
use Interne\FinancesBundle\Entity\Facture;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class RappelController
 * @package Interne\FinancesBundle\Controller
 *
 * @Route("/rappel")
 */
class RappelController extends Controller
{

    /**
     * @Route("/get_form_ajax", name="interne_finance_rappel_get_form_ajax", options={"expose"=true})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sendFormAjaxAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $idFacture = $request->request->get('idFacture');


            $rappel = new Rappel();

            $rappelForm  = $this->createForm(new RappelType,$rappel);

            return $this->render('InterneFinancesBundle:Rappel:modalFormRappel.html.twig',
                array('form' => $rappelForm->createView(),
                    'idFacture'=> $idFacture));

        }
       return new Response();
    }

    /**
     * @Route("/add_ajax", name="interne_finance_rappel_add_ajax", options={"expose"=true})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addRappel(Request $request)
    {

        if ($request->isXmlHttpRequest()) {

            //On récupère les données du formulaire
            $rappel = new Rappel();
            $rappelForm  = $this->createForm(new RappelType,$rappel);
            $rappelForm->submit($request);
            $rappel = $rappelForm->getData();


            //On récupère la facture à laquel on ajoute le rappel
            $idFacture = $request->request->get('idFacture');
            $em = $this->getDoctrine()->getManager();
            $facture = $em->getRepository('InterneFinancesBundle:Facture')->find($idFacture);


            //on vérifie que la facture est ouverte.
            if($facture->getStatut() == 'ouverte')
            {
                //Ajout du rappel
                $facture->addRappel($rappel);
            }


            $em->flush();


            return new Response('success');

        }
        return new Response('error');
    }





}
