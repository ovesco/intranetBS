<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Groupe;
use AppBundle\Entity\GroupeModel;
use AppBundle\Form\GroupeModelType;
use AppBundle\Form\GroupeType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\FonctionType;
use AppBundle\Entity\Fonction;

/**
 * Class StructureController
 * @package AppBundle\Controller
 *
 * @Route("/structure")
 */
class StructureController extends Controller
{
    /**
     * Page qui affiche la hierarchie des groupes
     *
     * @Route("/gestion_groupe", name="structure_gestion_groupe", options={"expose"=true})
     * @param Request $request
     * @return Response
     *
     */
    public function gestionGroupeAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $hiestGroupes = $em->getRepository('AppBundle:Groupe')->findHighestGroupes();

        return $this->render('AppBundle:Structure:page_gestionGroupe.html.twig', array(
            'highestGroupes' => $hiestGroupes
        ));


    }


    /**
     * Page qui affiche les fonctions
     *
     * @Route("/gestion_fonction", name="structure_gestion_fonctions", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     *
     */
    public function gestionFonctionAction(Request $request) {

        $em = $this->getDoctrine()->getManager();


        //retourne toutes les fonctions
        $fonctions = $em->getRepository('AppBundle:Fonction')->findAll();

        return $this->render('AppBundle:Structure:page_gestionFonction.html.twig',array(
            'fonctions' =>$fonctions));


    }

    /**
     * Page qui affiche les models de groupes
     *
     * @Route("/gestion_model", name="structure_gestion_model", options={"expose"=true})
     * @param Request $request
     * @return Response
     *
     */
    public function gestionModelAction(Request $request) {

        $em = $this->getDoctrine()->getManager();


        //retourne toutes les fonctions
        $models = $em->getRepository('AppBundle:Model')->findAll();

        return $this->render('AppBundle:Structure:page_gestionModel.html.twig',array(
            'models' =>$models));


    }



}