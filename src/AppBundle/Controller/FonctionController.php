<?php

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Utils\Menu\Menu;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\Fonction\FonctionType;
use AppBundle\Entity\Fonction;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Utils\Response\ResponseFactory;

/**
 * Class StructureController
 * @package AppBundle\Controller
 *
 * @Route("/intranet/fonctions")
 */
class FonctionController extends Controller
{

    /**
     * Page qui affiche les fonctions
     *
     * @Route("/gestion", options={"expose"=true})
     * @param Request $request
     * @return Response
     * @Menu("Gestion des fonctions", block="structure", order=2, icon="tag")
     * @Template("AppBundle:Fonction:page_gestion.html.twig")
     */
    public function gestionAction(Request $request) {

        return array();
    }

    /**
     * @Route("/add", options={"expose"=true})
     * @param Request $request
     * @return Response
     * @Template("AppBundle:Fonction:form_modal.html.twig")
     */
    public function addAction(Request $request)
    {
        $fonction = new Fonction();
        $addForm = $this->createForm(new FonctionType(),$fonction,array('action' => $this->generateUrl('app_fonction_add')));

        $addForm->handleRequest($request);

        if($addForm->isValid())
        {
            $this->get('app.repository.fonction')->save($fonction);
            return $this->redirect($this->generateUrl('app_fonction_gestion'));
        }

        return array('form'=>$addForm->createView());
    }

    /**
     * @Route("/edit/{fonction}", options={"expose"=true})
     * @param Request $request
     * @param Fonction $fonction
     * @return Response
     * @ParamConverter("fonction", class="AppBundle:Fonction")
     * @Template("AppBundle:Fonction:form_modal.html.twig")
     */
    public function editAction(Request $request,Fonction $fonction)
    {

        $editedForm = $this->createForm(
            new FonctionType(),
            $fonction,
            array('action' => $this->generateUrl('app_fonction_edit',array('fonction'=>$fonction->getId()))));

        $editedForm->handleRequest($request);

        if($editedForm->isValid())
        {
            $this->get('app.repository.fonction')->save($fonction);
            return $this->redirect($this->generateUrl('app_fonction_gestion'));

        }

        return array('form'=>$editedForm->createView());
    }

    /**
     * @Route("/remove/{fonction}", options={"expose"=true})
     * @param Request $request
     * @param Fonction $fonction
     * @return Response
     * @ParamConverter("fonction", class="AppBundle:Fonction")
     * @Template("AppBundle:Fonction:form_modal.html.twig")
     */
    public function removeAction(Request $request,Fonction $fonction)
    {
        if($fonction->isRemovable())
        {
            $this->get('app.repository.fonction')->remove($fonction);
            return ResponseFactory::ok();
        }
        else
        {
            return ResponseFactory::forbidden();
        }
    }

}