<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Membre;
use AppBundle\Entity\Attribution;
use AppBundle\Form\AttributionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AttributionController
 * @package AppBundle\Controller
 *
 * @Route("/attribution")
 */
class AttributionController extends Controller
{

    /**
     * @Route("/get-modal", name="attribution_get_modal", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     */
    public function getAttributionFormAjaxAction(Request $request)
    {

        //if ($request->isXmlHttpRequest()) {

        $em = $this->getDoctrine()->getManager();
        /*
         * On envoie le formulaire en modal
         */

        $idMembre = $request->request->get('idMembre');
        $idAttribution = $request->request->get('idAttribution');

        $attribution = null;
        $attributionForm = null;
        if ($idAttribution == null) {
            /*
             * Ajout
             */
            $attribution = new Attribution();
            $attribution->setMembre($idMembre);
            $attributionForm = $this->createForm(new AttributionType(), $attribution,
                array('action' => $this->generateUrl('attribution_add', array('member'=>$idMembre))));

        } else {
            /*
             * Modification
             */
            //TODO: pas testé
            $attribution = $em->getRepository('AppBundle:Attribution')->find($idAttribution);
            $attributionForm = $this->createForm(new AttributionType(), $attribution,
                array('action' => $this->generateUrl('attribution_edit', array('attribution'=>$idAttribution))));

        }

        return $this->render('AppBundle:Attribution:attribution_form_modal.html.twig', array(
                'form' => $attributionForm->createView())
        );

    }


    /**
     * @Route("/add", name="attribution_add", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     */
    public function addAttributionAction(Request $request)
    {

        $newAttribution = new Attribution();
        $newAttributionForm = $this->createForm(new AttributionType(), $newAttribution);

        $newAttributionForm->handleRequest($request);

        if($newAttributionForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newAttribution);
            $em->flush();

            return new Response(true);
        }

        return new Response(false);
    }

    /**
     * @Route("/edit/{attribution}", name="attribution_edit", options={"expose"=true})
     *
     * @param Request $request
     * @param Attribution $attribution
     * @return Response
     * @ParamConverter("attribution", class="AppBundle:Attribution")
     */
    public function editAttribution(Attribution $attribution, Request $request)
    {
        //TODO: modifier une attribution (ou peut-être ne veut-on que les supprimer ?)
    }

    //TODO : remove
}

?>