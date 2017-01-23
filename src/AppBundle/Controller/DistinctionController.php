<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Distinction;
use AppBundle\Voters\CRUD;
use AppBundle\Voters\DistinctionVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils\Menu\Menu;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Form\Distinction\DistinctionType;
use AppBundle\Utils\Response\ResponseFactory;
/**
 * Class ContactController
 * @package AppBundle\Controller
 *
 * @Route("/intranet/structure/distinctions")
 */
class DistinctionController extends Controller{

    /**
     * Page qui affiche les distinctions
     *
     * @Route("/gestion", options={"expose"=true})
     * @param Request $request
     * @return Response
     * @Menu("Gestion des distinctions", block="structure", order=5, icon="star")
     * @Template("AppBundle:Distinction:page_gestion.html.twig")
     */
    public function gestionAction(Request $request) {

        return array();
    }


    /**
     * @Route("/add", options={"expose"=true})
     * @param Request $request
     * @return Response
     * @Template("AppBundle:Distinction:form_modal.html.twig")
     */
    public function addAction(Request $request)
    {
        $distinction = new Distinction();

        $this->denyAccessUnlessGranted(CRUD::CREATE,$distinction);

        $form = $this->createForm(new DistinctionType(),$distinction,array('action' => $this->generateUrl('app_distinction_add')));

        $form->handleRequest($request);

        if($form->isValid())
        {
            $this->get('app.repository.distinction')->save($distinction);
            return $this->redirect($this->generateUrl('app_distinction_gestion'));
        }

        return array('form'=>$form->createView());
    }

    /**
     * @Route("/edit/{distinction}", options={"expose"=true})
     * @param Request $request
     * @param Distinction $distinction
     * @return Response
     * @ParamConverter("distinction", class="AppBundle:Distinction")
     * @Template("AppBundle:Distinction:form_modal.html.twig")
     */
    public function editAction(Request $request,Distinction $distinction)
    {
        $this->denyAccessUnlessGranted(CRUD::UPDATE,$distinction);

        $editedForm = $this->createForm(
            new DistinctionType(),
            $distinction,
            array('action' => $this->generateUrl('app_distinction_edit',array('distinction'=>$distinction->getId()))));

        $editedForm->handleRequest($request);

        if($editedForm->isValid())
        {
            $this->get('app.repository.distinction')->save($distinction);
            return $this->redirect($this->generateUrl('app_distinction_gestion'));

        }

        return array('form'=>$editedForm->createView());
    }

    /**
     * @Route("/remove/{distinction}", options={"expose"=true})
     * @param Request $request
     * @param Distinction $distinction
     * @return Response
     * @ParamConverter("distinction", class="AppBundle:Distinction")
     */
    public function removeAction(Request $request,Distinction $distinction)
    {
        $this->denyAccessUnlessGranted(CRUD::DELETE,$distinction);

        if(!$distinction->isRemovable())
        {
            return ResponseFactory::forbidden();
        }

        $this->get('app.repository.distinction')->remove($distinction);
        return ResponseFactory::ok();
    }

}