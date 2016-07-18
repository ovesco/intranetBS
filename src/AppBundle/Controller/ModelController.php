<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Model;
use AppBundle\Form\Model\ModelType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils\Menu\Menu;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Utils\Response\ResponseFactory;
use Doctrine\ORM\EntityManager;

/**
 * Class ModelController
 * @package AppBundle\Controller
 *
 * @Route("/intranet/model")
 */
class ModelController extends Controller
{

    /**
     * Page qui affiche les models de groupes
     *
     * @Route("/gestion", options={"expose"=true})
     * @param Request $request
     * @return Response
     * @Menu("Gestion des models", block="structure", order=3, icon="bookmark")
     * @Template("AppBundle:Model:page_gestion.html.twig")
     */
    public function gestionAction(Request $request) {
        return array();
    }


    /**
     * @Route("/add", options={"expose"=true})
     * @Template("AppBundle:Model:form_modal.html.twig")
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request)
    {
        $model = new Model();
        $modelForm = $this->createForm(new ModelType(),$model,array('action' => $this->generateUrl('app_model_add')));

        $modelForm->handleRequest($request);

        if($modelForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($model);
            $em->flush();
            return ResponseFactory::ok();
        }

        return array('form'=>$modelForm->createView());
    }

    /**
     * @Route("/edit/{model}", options={"expose"=true})
     * @Template("AppBundle:Model:form_modal.html.twig")
     * @param Request $request
     * @param Model $model
     * @return Response
     * @ParamConverter("model", class="AppBundle:Model")
     */
    public function editAction(Request $request,Model $model)
    {

        $editedForm = $this->createForm(new ModelType(),$model,array('action' => $this->generateUrl('app_model_edit',array('model'=>$model->getId()))));

        $editedForm->handleRequest($request);

        if($editedForm->isValid())
        {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $em->persist($model);
            $em->flush();
            return ResponseFactory::ok();
        }
        return array('form'=>$editedForm->createView());
    }

    /**
     * @Route("/edit/{model}", options={"expose"=true})
     * @Template("AppBundle:Model:form_modal.html.twig")
     * @param Request $request
     * @param Model $model
     * @return Response
     * @ParamConverter("model", class="AppBundle:Model")
     */
    public function removeAction(Request $request,Model $model)
    {
        if($model->isRemovable())
        {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $em->remove($model);
            $em->flush();
            return ResponseFactory::ok();
        }
        else
        {
            return ResponseFactory::forbidden();
        }
    }

}