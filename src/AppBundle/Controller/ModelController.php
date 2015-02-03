<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Model;
use AppBundle\Form\ModelType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class ModelController
 * @package AppBundle\Controller
 *
 * @Route("/model")
 */
class ModelController extends Controller
{

    /**
     * @Route("/get_form_modale", name="model_get_form_modale", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     */
    public function getGroupeModelFormAjaxAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {

            $em = $this->getDoctrine()->getManager();

            /*
             * On envoie le formulaire en modal
             */
            $id = $request->request->get('idModel');

            $model = null;
            $modelForm = null;
            if($id == null)
            {
                /*
                 * Ajout
                 */
                $model = new Model();
                $modelForm = $this->createForm(new ModelType(),$model,
                    array('action' => $this->generateUrl('model_add')));
            }
            else
            {
                $model = $em->getRepository('AppBundle:Model')->find($id);
                $modelForm = $this->createForm(new ModelType(),$model,
                    array('action' => $this->generateUrl('model_edit',array('model'=>$id))));
            }

            return $this->render('AppBundle:Model:model_modale_form.html.twig',array('form'=>$modelForm->createView()));

        }
        return new Response();
    }

    /**
     * @Route("/add", name="model_add", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     */
    public function addGroupeModelAction(Request $request)
    {
        $newModel = new Model();
        $newModelForm = $this->createForm(new ModelType(),$newModel);

        $newModelForm->handleRequest($request);

        if($newModelForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newModel);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('structure_gestion_model'));
    }

    /**
     * @Route("/edit/{model}", name="model_edit", options={"expose"=true})
     *
     * @param Request $request
     * @param Model $model
     * @return Response
     * @ParamConverter("model", class="AppBundle:Model")
     */
    public function editFonctionction(Model $model,Request $request)
    {

        $editedForm = $this->createForm(new ModelType(),$model);

        $editedForm->handleRequest($request);

        if($editedForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

        }

        return $this->redirect($this->generateUrl('structure_gestion_model'));
    }

}