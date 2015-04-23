<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidationController extends Controller
{

    /**
     * Permet de gérer les modifications et les validations
     * @route("validation/global-view", name="validation_vue_globale")
     */
    public function validationViewAction() {

        $em = $this->getDoctrine()->getManager();

        return $this->render('AppBundle:Validation:global_view.html.twig', array(
            'containers' => $em->getRepository('AppBundle:ModificationsContainer')->findAll()
        ));
    }


    /**
     * Permet de modifier une valeur sur une entité, en appelant en passant par le service de validation.
     * Les requêtes sont efféctuées en AJAX à l'aide de X::Editable. La particularité de ce système est que
     * la route est unique, et tous les paramètres sont transmis à l'aide de GET
     *
     * @route("validation/ajax/modify-property", name="interne_ajax_app_modify_property", options={"expose"=true})
     * @return JsonResponse
     */
    public function modifyPropertyAction(Request $request) {

        $id    = $request->get('pk');
        $value = $request->get('value');

        $schem = explode('_', $request->get('name'));


        // On nettoie le schem afin de l'utiliser
        $path  = $schem[1] . '.' . $id . '.' . $schem[2];

        $validator      = $this->get('validation');
        $requiredPaths  = $validator->validateField($value, $path);

        return new JsonResponse();

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
     * @route("validation/modification/approve/{modification}", name="validation_ajax_approve_modification", options={"expose"=true})
     * @ParamConverter("modification", class="AppBundle:Modification")
     */
    public function approveModification($modification) {

        $em = $this->getDoctrine()->getManager();
        $em->remove($modification);
        $em->flush();

        return new JsonResponse(true);
    }

    /**
     * Annule une modification, vérifie la stabilité des données après coup
     * @param Modification $modification
     * @return Response
     * @route("validation/modification/cancel/{modification}", name="validation_ajax_cancel_modification", options={"expose"=true})
     * @ParamConverter("modification", class="AppBundle:Modification")
     */
    public function cancelModification($modification) {

        $this->get('validation')->cancelModification($modification);
        return new JsonResponse(true);
    }
}
