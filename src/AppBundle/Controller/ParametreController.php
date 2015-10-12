<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/* routing */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/* Entity */
use AppBundle\Entity\Parameter;

/* Form */
use AppBundle\Form\ParameterType;

/**
 * Ce controller est utilisé pour la gestion des pages de list et d'édition des parametres de l'applications.
 *
 * Class ParametreController
 * @package AppBundle\Controller
 * @Route("/parametre")
 */
class ParametreController extends Controller
{

    /**
     * @Route("/list", name="interne_parametre_list")
     * @Template("AppBundle:Parametre:page_list.html.twig")
     *
     * @return Response
     */
    public  function listAction()
    {
        $parameters = $this->getDoctrine()->getRepository('AppBundle:Parameter')->findAll();
        return array('parameters' => $parameters);
    }




    /**
     * @Route("/edit/{parameter}", name="interne_parametre_edit")
     * @Template("AppBundle:Parametre:page_edit.html.twig")
     * @param Parameter $parameter
     * @ParamConverter("parameter", class="AppBundle:Parameter")
     * @param Request $request
     * @return Response
     */
    public  function editAction(Request $request,Parameter $parameter)
    {
        $form = $this->createForm(new ParameterType(),$parameter);

        $form->handleRequest($request);


        if($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($parameter);
            $em->flush();

            return $this->redirect($this->generateUrl('interne_parametre_list'));
        }

        return array('form'=>$form->createView());

    }






}