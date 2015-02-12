<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ObtentionDistinction;
use AppBundle\Form\ObtentionDistinctionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ObtentionDistinctionController
 * @package AppBundle\Controller
 *
 * @Route("/obtention-distinction")
 */
class ObtentionDistinctionController extends Controller
{

    /**
     * @Route("/get-modal", name="obtention-distinction_get_modal", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     */
    public function getObtentionDistinctionFormAjaxAction(Request $request)
    {

        //if ($request->isXmlHttpRequest()) {

            $em = $this->getDoctrine()->getManager();
            /*
             * On envoie le formulaire en modal
             */
            $id = $request->request->get('idObtentionDistinction');

            $distinctions = $this->getDoctrine()->getRepository('AppBundle:Distinction')->findAll();

            $obtention = null;
            $obtentionForm = null;
            if ($id == null) {
                /*
                 * Ajout
                 */
                $obtention = new ObtentionDistinction();
                $obtentionForm = $this->createForm(new ObtentionDistinctionType(), $obtention,
                    array('action' => $this->generateUrl('obtention-distinction_add')));

            } else {
                /*
                 * Modification
                 */
                //TODO: pas testé
                $obtention = $em->getRepository('AppBundle:ObtentionDistinction')->find($id);
                $obtentionForm = $this->createForm(new ObtentionDistinctionType(), $obtention,
                    array('action' => $this->generateUrl('obtention-distinction_edit',array('obtention'=>$id))));

            }

            return $this->render('AppBundle:ObtentionDistinction:obtention-distinctions_form_modal.html.twig', array(
                    'distinctions' => $distinctions,
                    'form' => $obtentionForm->createView())
            );

        //}
    }


    /**
     * @Route("/add", name="obtention-distinction_add", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     */
    public function addObtentionDistinctionAction(Request $request)
    {
        //TODO: ajouter l'obtenition
    }

    /**
     * @Route("/edit/{obtention-distinction}", name="obtention-distinction_edit", options={"expose"=true})
     *
     * @param Request $request
     * @param ObtentionDistinction $obtention
     * @return Response
     * @ParamConverter("obtention-distinction", class="AppBundle:ObtentionDistinction")
     */
    public function editObtentionDistinction(ObtentionDistinction $obtention, Request $request)
    {
        //TODO: modifier une obtention (ou peut-être ne veut-on que les supprimer ?)
    }

    //TODO : remove
}

?>