<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Groupe;
use AppBundle\Entity\Type;
use AppBundle\Form\GroupeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\FonctionType;
use AppBundle\Entity\Fonction;
use AppBundle\Form\TypeType;

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
     * @Route("/hierarchie", name="structure_hierarchie")
     * @Template("AppBundle:Structure:page_hierarchie.html.twig")
     * @param Request $request
     * @return Response
     *
     */
    public function hierarchieAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $groupeRepo = $em->getRepository('AppBundle:Groupe');

        //retourne les groupes parents de toute la structure
        $hiestGroupes = $groupeRepo->findHighestGroupes();

        return array(
            'highestGroupes' =>$hiestGroupes
        );

    }

    /**
     * Page qui affiche la hierarchie des groupes
     *
     * @Route("/gestion_fonction", name="structure_gestion_fonctions")
     * @Template("AppBundle:Structure:page_gestionFonction.html.twig")
     * @param Request $request
     * @return Response
     *
     */
    public function gestionFonctionAction(Request $request) {

        $em = $this->getDoctrine()->getManager();

        $fonction = new Fonction();
        $fonctionForm = $this->createForm(new FonctionType,$fonction);

        if($request->request->has($fonctionForm->getName()))
        {
            $fonctionForm->handleRequest($request);

            if ($fonctionForm->isValid()) {

                $em->persist($fonction);
                $em->flush();

                return $this->redirect($this->generateUrl('structure_gestion_fonctions'));
            }
        }

        //retourne toutes les fonctions
        $fonctions = $em->getRepository('AppBundle:Fonction')->findAll();

        return array(
            'fonctions' =>$fonctions,'fonctionForm'=>$fonctionForm->createView()
        );

    }

    /**
     * Page qui affiche la hierarchie des groupes
     *
     * @Route("/gestion_type_groupe", name="structure_gestion_type_groupe")
     * @Template("AppBundle:Structure:page_gestionTypeGroupes.html.twig")
     * @param Request $request
     * @return Response
     *
     */
    public function gestionGroupeTypeAction(Request $request) {


        //retourne toutes les fonctions
        $types = $this->getDoctrine()->getRepository('AppBundle:Type')->findAll();


        return array(
            'types' =>$types
        );

    }


}