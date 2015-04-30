<?php

namespace AppBundle\Controller;

use AppBundle\Form\VoirMembreType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidationController extends Controller
{

    /**
     * Permet de modifier une valeur sur une entité.
     * Les requêtes sont efféctuées en AJAX à l'aide de X::Editable. Pour changer la valeur, on génère un formulaire
     * dynamique pour le champ voulu, on le modifie, et BAM ca fait des PUTAIN de chocapics.
     *
     * @route("validation/ajax/modify-property", name="interne_ajax_app_modify_property", options={"expose"=true})
     * @return JsonResponse
     */
    public function modifyPropertyAction(Request $request) {


        $id     = $request->get('pk');                   // L'id de l'entité à modifier
        $value  = $request->get('value');                // la nouvelle valeur
        $data   = $request->get('name');                 // Les données utiles



        /*
        $id     = 3;
        $data   = '';
        $value  = 'yolololol';
        */



        $data   = explode('___', $data);                 // separation des données utiles
        $type   = $data[0];                              // le type (parmis les types de form comme entity, text...)
        $class  = $data[1];
        $field  = $data[2];



        $em     = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:' . $class)->find($id);


        $form = $this->createFormBuilder($entity, array('csrf_protection' => false))->add($field, $type)->getForm();
        $form->submit( array($field => $value) );

        if($form->isValid()) {

            echo 'valid';
            $em->persist($entity);
            $em->flush();
        }

        else {

            echo $form->getErrorsAsString();
            return new Response('', 401);
        }


        return new Response();
    }


    private function getLinkedEntity($id, $name) {

        // On récupère les informations de base, c'est-à-dire le bundle et l'entité mère
        $bundle     = explode('_', $name)[0];
        $realDeal   = explode('_', str_replace($bundle . '_', '', $name));

        $em         = $this->getDoctrine()->getManager();
        $cursor     = null;


        /*
         * On itère sur l'ensemble du chemin. A chaque fois on récupère l'entité liée
         * jusqu'à arriver au dernier élément
         */
        for($i = 0; $i < count($realDeal); $i++) {

            $current = $realDeal[$i];

            /*
             * Premier test, on vérifie que le curseur soit bien hydraté, c'est-à-dire que
             * l'entité mère ait été définie. Sans quoi on la récupère et la stockons dans le curseur
             */
            if($cursor == null) {

                $cursor = $em->getRepository($bundle . ':' . ucfirst($current))->find($id);
                continue;
            }


            /*
             * On vérifie ensuite que l'on ait pas par hasard atteint la fin de la chaine,
             * c'est-à-dire la proprieté voulu sur l'entité que l'on cherche. Si c'est le cas,
             * on retourne le curseur courant.
             */
            if($i == (count($realDeal) -1)) // C'est la proprieté à modifier, donc on retourne l'entité courante
                return array('entity' => $cursor, 'property' => $current);


            /*
             * Sinon on analyse la valeur du $current. Si il s'agit d'une string claire, alors on a affaire à un GET
             * sur le curseur. Si par contre on a une valeur numérique, on doit réaliser un get sur un array.
             */
            if(is_numeric($current))
                $cursor = $cursor[intval($current)];



            else {
                $getter = 'get' . ucfirst($current);
                $cursor = $cursor->$getter();
            }
        }
    }




































































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
