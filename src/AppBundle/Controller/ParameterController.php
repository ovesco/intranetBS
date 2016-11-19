<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/* Annotation */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Utils\Menu\Menu;

/* Entity */
use AppBundle\Entity\Parameter;

/* Form */
use AppBundle\Form\Parameter\ParameterType;

/**
 * Ce controller est utilisé pour la gestion des pages de list et d'édition des parametres de l'applications.
 *
 *
 * todo NUR passer un coup dans ce controller pour un affichage des parametr comme ListModelsParameter.
 * ainsi qu'une édition par modal des parametre... ca serais plus générique
 *
 * Class ParameterController
 * @package AppBundle\Controller
 * @Route("/intranet/parameter")
 */
class ParameterController extends Controller
{

    /**
     * @Route("/list")
     * @Template("AppBundle:Parametre:page_list.html.twig")
     * @Menu("Configuration", block="parameter", icon="configure")
     * @return Response
     */
    public  function listAction()
    {
        $parameters = $this->getDoctrine()->getRepository('AppBundle:Parameter')->findAll();
        return array('parameters' => $parameters);
    }

    /**
     * @Route("/edit/{parameter}", options={"expose"=true})
     * @Template("AppBundle:Parametre:modal_edit.html.twig")
     * @param Parameter $parameter
     * @ParamConverter("parameter", class="AppBundle:Parameter")
     * @param Request $request
     * @return Response
     */
    public  function editAction(Request $request,Parameter $parameter)
    {
        $form = $this->createForm(new ParameterType(),$parameter,
            array('action'=>$this->generateUrl('app_parameter_edit',array('parameter'=>$parameter->getId()))));

        $form->handleRequest($request);

        if($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($parameter);
            $em->flush();

            return $this->redirect($this->generateUrl('app_parameter_list'));
        }

        return array('form'=>$form->createView());

    }






}