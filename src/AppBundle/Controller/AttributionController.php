<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Membre;
use AppBundle\Entity\Attribution;
use AppBundle\Form\AttributionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        $em = $this->getDoctrine()->getManager();
        /*
         * On envoie le formulaire en modal
         */

        $idMembre = $request->request->get('idMembre');
        $idAttribution = $request->request->get('idAttribution');

        $multiMembre = false;
        $multiMembreIds = null;
        $attribution = null;
        $attributionForm = null;
        if ($idAttribution == null) {
            /*
             * Ajout
             */
            $attribution = new Attribution();

            if($idMembre !== null) {
                if( is_array($idMembre) ) {
                    $multiMembre = true;
                    $multiMembreIds = implode(",", $idMembre);
                } else {
                    $attribution->setMembre($em->getRepository('AppBundle:Membre')->find($idMembre));
                }
            }

            $attributionForm = $this->createForm(new AttributionType(), $attribution, array(
                'action'    => $this->generateUrl('attribution_add'),
                'attr'      => array(
                    'multiMembre'       => $multiMembre,
                    'multiMembreIds'    => $multiMembreIds
                )
            ));

        } else {
            /*
             * Modification
             */
            //TODO: pas testé
            $attribution = $em->getRepository('AppBundle:Attribution')->find($idAttribution);
            $attributionForm = $this->createForm(new AttributionType(), $attribution,
                array('action' => $this->generateUrl('attribution_edit')));

        }

        return $this->render('AppBundle:Attribution:attribution_form_modal.html.twig', array(
            'form' => $attributionForm->createView(),
            'postform' => $attributionForm)
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

            return new JsonResponse(true);
        }

        return $this->render('AppBundle:Attribution:attribution_form_modal.html.twig', array(
            'form' => $newAttributionForm->createView(),
            'postform' => $newAttributionForm));
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