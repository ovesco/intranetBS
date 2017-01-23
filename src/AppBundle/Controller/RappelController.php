<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Rappel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Form\Rappel\RappelType;
use AppBundle\Entity\Facture;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class RappelController
 * @package AppBundle\Controller
 *
 * @Route("/intranet/finance/rappel")
 */
class RappelController extends Controller
{

    /**
     * @Route("/send_form_ajax", options={"expose"=true})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * todo NUR repasser par ici...
     */
    public function sendFormAjaxAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $idFacture = $request->request->get('idFacture');


            $rappel = new Rappel();

            $rappelForm  = $this->createForm(new RappelType,$rappel);

            return $this->render('AppBundle:Rappel:modalFormRappel.html.twig',
                array('form' => $rappelForm->createView(),
                    'idFacture'=> $idFacture));

        }
       return new Response();
    }

    /**
     * @Route("/add_ajax", options={"expose"=true})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * todo NUR repasser par ici...
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
            $facture = $em->getRepository('AppBundle:Facture')->find($idFacture);


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
