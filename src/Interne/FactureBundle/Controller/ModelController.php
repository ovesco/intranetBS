<?php

namespace Interne\FactureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Interne\FactureBundle\Entity\Model;
use Interne\FactureBundle\Form\ModelType;
use Symfony\Component\HttpFoundation\Request;

class ModelController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $models = $em->getRepository('InterneFactureBundle:Model')->findAll();

        return $this->render('InterneFactureBundle:Model:index.html.twig', array('models' => $models));
    }

    public function createAction(Request $request)
    {
        $model = new Model();


        $modelForm  = $this->createForm(new ModelType, $model);

        if ($request->isMethod('POST'))
        {
            $modelForm->submit($request);

            if ($modelForm->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $em->persist($model);
                $em->flush();

                return $this->redirect($this->generateUrl('interne_facture_model'));
            }
        }

        return $this->render('InterneFactureBundle:Model:create.html.twig', array(
            'modelForm' => $modelForm->createView()
        ));
    }

    public function updateAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $model = $em->getRepository('InterneFactureBundle:Model')->find($id);
        $modelForm  = $this->createForm(new ModelType, $model);

        $request = $this->get('request');
        if ($request->isMethod('POST'))
        {
            $modelForm->submit($request);

            if ($modelForm->isValid()) {


                $em->flush();

                return $this->redirect($this->generateUrl('interne_facture_model'));
            }
        }

        return $this->render('InterneFactureBundle:Model:update.html.twig', array(
            'modelForm' => $modelForm->createView()
        ));
    }

    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $model = $em->getRepository('InterneFactureBundle:Model')->find($id);

        //on verifie que la facture existe bien, si c'est pas le cas, on affiche l'index
        if($model == Null)
        {
            return $this->redirect($this->generateUrl('interne_facture_model'));
        }

        return $this->render('InterneFactureBundle:Model:show.html.twig', array('model' => $model));

    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $model = $em->getRepository('InterneFactureBundle:Model')->find($id);

        if($model != Null)
        {
            $em->remove($model);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('interne_facture_model'));
    }
}
