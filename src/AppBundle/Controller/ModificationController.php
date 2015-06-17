<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Membre;
use AppBundle\Entity\Modification;
use AppBundle\Form\VoirMembreType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ModificationController extends Controller
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
        $data   = $request->get('name');                 // Les données utile


        /*
        $id     = 1;
        $data   = 'text___Adresse___rue';
        $value  = 'Rue des poireaux ' . time();
        */


        $data   = explode('___', $data);                 // separation des données utiles
        $type   = $data[0];                              // le type (parmis les types de form comme entity, text...)
        $class  = $data[1];                              // la classe
        $field  = $data[2];                              // Le champ modifié

        $em     = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:' . $class)->find($id);

        $getter = 'get' . ucfirst($field);               // On génère un getter pour obtenir l'ancienne valeur avant modif
        $oldVal = $entity->$getter();                    // On récupère l'ancienne valeur


        $form   = $this->createFormBuilder($entity, array('csrf_protection' => false))->add($field, $type)->getForm();
        $form->submit( array($field => $value) );

        /*
         * Formulaire valide, on valide la modification, et on crée une entrée de modification qui permettra
         * au SG de valider ou pas ces changements
         */
        if($form->isValid()) {

            $verification = $this->get('verification');
            //$verification->addModification($field, $oldVal, $entity);



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


    /**
     * Permet de gérer les modifications
     * @route("modification/global-view", name="interne_modification_vue_globale")
     */
    public function modificationViewAction() {

        $em             = $this->getDoctrine()->getManager();
        $modifications  = $em->getRepository('AppBundle:Modification')->findByStatut(Modification::EN_ATTENTE);
        $twigModif      = $this->get('app.twig.validation_extension');
        $jsoned         = array();
        $concerned      = function($path) use ($em) {
            $data = explode('.', $path);
            return $this->getDoctrine()->getRepository('AppBundle:' . ucfirst($data[0]))->find($data[1]);
        };

        foreach($modifications as $modif) {

            $ccc = $concerned($modif->getPath());

            $jsoned[] = array(
                'id'        => $modif->getId(),
                'auteur'    => array('nom' => $modif->getAuteur()->__toString(), 'id' => $modif->getAuteur()->getId()),
                'concerned' => array('type' => ($ccc instanceof Membre)? 'membre' : 'famille', 'id' => $ccc->getId(), 'path' => $twigModif->pathToString($modif->getPath())),
                'values'    => array('old' => $modif->getOldValue(), 'new' => $modif->getNewValue()),
                'date'      => $modif->getDate()->format('d.m.Y')
            );
        }


        return $this->render('AppBundle:Modifications:page_gestion_modifications.html.twig', array(

            'jsonModifications' => json_encode($jsoned)
        ));
    }
}
