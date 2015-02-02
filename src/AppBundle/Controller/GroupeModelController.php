<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Groupe;
use AppBundle\Entity\GroupeModel;
use AppBundle\Form\GroupeModelType;
use AppBundle\Form\GroupeType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\FonctionType;
use AppBundle\Entity\Fonction;

/**
 * Class StructureController
 * @package AppBundle\Controller
 *
 * @Route("/groupe_model")
 */
class GroupeModelController extends Controller
{

    /**
     * @Route("/get_form_modale", name="groupe_model_get_form_modale", options={"expose"=true})
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
            $id = $request->request->get('idGroupeModel');

            $model = null;
            $modelForm = null;
            if($id == null)
            {
                /*
                 * Ajout
                 */
                $model = new GroupeModel();
                $modelForm = $this->createForm(new GroupeModelType(),$model,
                    array('action' => $this->generateUrl('groupe_model_add')));
            }
            else
            {
                $model = $em->getRepository('AppBundle:GroupeModel')->find($id);
                $modelForm = $this->createForm(new GroupeModelType(),$model,
                    array('action' => $this->generateUrl('groupe_model_edit',array('groupeModel'=>$id))));
            }

            return $this->render('AppBundle:GroupeModel:groupe_model_modale_form.html.twig',array('form'=>$modelForm->createView()));

        }
    }

    /**
     * @Route("/add", name="groupe_model_add", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     */
    public function addGroupeModelAction(Request $request)
    {
        $newModel = new GroupeModel();
        $newModelForm = $this->createForm(new GroupeModelType(),$newModel);

        $newModelForm->handleRequest($request);

        if($newModelForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newModel);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('structure_gestion_groupe_model'));
    }

    /**
     * @Route("/edit/{groupeModel}", name="groupe_model_edit", options={"expose"=true})
     *
     * @param Request $request
     * @param GroupeModel $groupeModel
     * @return Response
     * @ParamConverter("groupeModel", class="AppBundle:GroupeModel")
     */
    public function editFonctionction(GroupeModel $groupeModel,Request $request)
    {

        $editedForm = $this->createForm(new GroupeModelType(),$groupeModel);

        $editedForm->handleRequest($request);

        if($editedForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

        }

        return $this->redirect($this->generateUrl('structure_gestion_groupe_model'));
    }

}