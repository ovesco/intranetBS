<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Fonction;
use Interne\SecurityBundle\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/* Annotation */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Utils\Menu\Menu;

/**
 * @Route("/interne/roles")
 * @package AppBundle\Controller
 */
class RolesController extends Controller
{

    /**
     * page permettant de lier des roles et des fonctions
     *
     * @Route("/matching_fonctions")
     * @Menu("Matching droit-fonctions", block="security", icon="compress")
     */
    public function matchingFonctionsAction(Request $request) {

        $em         = $this->getDoctrine()->getManager();
        $roles      = $em->getRepository('AppBundle:Role')->findAll();
        $fonctions  = $em->getRepository('AppBundle:Fonction')->findAll();

        if($request->request->get('matching-fonction-id') != null && $request->request->get('matching-linked-role') != null){ //On a un role à lier à une fonction

            $fonction = $em->getRepository('AppBundle:Fonction')->find($request->request->get('matching-fonction-id'));
            $role     = $em->getRepository('AppBundle:Role')->find($request->request->get('matching-linked-role'));

            $fonction->addRole($role);
            $em->persist($fonction);
            $em->flush();

            return $this->redirect($this->generateUrl('app_roles_matchingfonctions'));
        }


        return $this->render('AppBundle:Roles:page_matching_fonctions.html.twig', array(

            'roles'         => $roles,
            'fonctions'     => $fonctions,
        ));
    }

    /**
     * Retire un role d'une fonction
     * @param Fonction $fonction
     * @param Role $role
     * @Route("/unlink-fonction/{fonction}/{role}", options={"expose"=true})
     * @ParamConverter("role", class="AppBundle:Role")
     * @ParamConverter("fonction", class="AppBundle:Fonction")
     * @return Response
     */
    public function unlinkRoleFonction(Fonction $fonction, Role $role) {

        $em = $this->getDoctrine()->getManager();
        $fonction->removeRole($role);

        $em->persist($fonction);
        $em->flush();

        return $this->redirect($this->generateUrl('app_roles_matchingfonctions'));
    }
}