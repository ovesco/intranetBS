<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ValidationController extends Controller
{

    /**
     * Permet de gérer les modifications et les validations
     * @route("validation/global-view", name="validation_vue_globale")
     */
    public function validationViewAction() {

        $em = $this->getDoctrine()->getManager();

        return $this->render('Validation/global_view.html.twig', array(


            'containers' => $em->getRepository('AppBundle:ModificationsContainer')->findAll()
        ));
    }


    /**
     * Permet de modifier une valeur sur une entité, en appelant en passant par le service de validation.
     * Les requêtes sont efféctuées en AJAX
     *
     * @param $path string le chemin qui ammène à la valeur à modifier
     * @param $value mixed la nouvelle valeur
     * @route("validation/ajax/modify-property/{path}/{value}", name="interne_ajax_app_modify_property", options={"expose"=true})
     * @return JsonResponse les paths requis pour pouvoir valider
     */
    public function modifyPropertyAction($path, $value) {

        $validator      = $this->get('validation');
        $requiredPaths  = $validator->validateField($value, $path);

        return new JsonResponse($requiredPaths);
    }

    /**
     * Approuve un container, ca veut dire simplement le supprimer
     * @param ModificationsContainer $container
     * @return Response
     * @route("validation/approve/container/{container}", name="validation_approve_container")
     * @ParamConverter("container", class="AppBundle:ModificationsContainer")
     */
    public function approveContainer($container) {

        $em = $this->getDoctrine()->getManager();
        $em->remove($container);
        $em->flush();

        return $this->redirect($this->generateUrl('validation_vue_globale'));
    }

    /**
     * Annule les modifications réalisées par un container. On va iterer sur les modifications qu'il contient et tout
     * annuler
     * @param ModificationsContainer $container
     * @return Response
     * @route("validation/cancel/container/{container}", name="validation_cancel_container")
     * @ParamConverter("container", class="AppBundle:ModificationsContainer")
     */
    public function cancelContainer($container) {

        $this->get('validation')->cancelContainer($container);
        return $this->redirect($this->generateUrl('validation_vue_globale'));
    }

    /**
     * Approuve une modification
     * @param Modification $modification
     * @return Response
     * @route("validation/approve/modification/{modification}", name="validation_approve_modification")
     * @ParamConverter("modification", class="AppBundle:Modification")
     */
    public function approveModification($modification) {

        $em = $this->getDoctrine()->getManager();
        $em->remove($modification);
        $em->flush();

        return $this->redirect($this->generateUrl('validation_vue_globale'));
    }

    /**
     * Annule une modification, vérifie la stabilité des données après coup
     * @param Modification $modification
     * @return Response
     * @route("validation/cancel/modification/{modification}", name="validation_cancel_modification")
     * @ParamConverter("modification", class="AppBundle:Modification")
     */
    public function cancelModification($modification) {

        $this->get('validation')->cancelModification($modification);
        return $this->redirect($this->generateUrl('validation_vue_globale'));
    }
}
