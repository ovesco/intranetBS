<?php

namespace AppBundle\Controller;

use AppBundle\Form\FamilleType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Membre;
use AppBundle\Entity\Famille;

/**
 * Le controller queryDataOnLoad permet de charger des données avec l'affichage des vues twig. Par exemple la liste
 * des distinctions à afficher dans la modale d'ajout de distinctions
 ** @package AppBundle\Controller
 * @route("/intranet")
 */

class QueryDataOnloadController extends Controller {

    /**
     * Retourne la modale d'ajout de distinctions
     */
    public function distinctionsAction() {

        $distinctions = $this->getDoctrine()->getRepository('AppBundle:Distinction')->findAll();

        return $this->render('AppBundle:Distinction:modale_form_distinctions.html.twig', array(

            'distinctions' => $distinctions
        ));
    }
}
