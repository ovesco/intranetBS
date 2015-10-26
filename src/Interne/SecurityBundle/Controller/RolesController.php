<?php

namespace Interne\SecurityBundle\Controller;

use AppBundle\Entity\Fonction;
use Interne\SecurityBundle\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @route("interne/roles/")
 * @package Interne\SecurityBundle\Controller
 */
class RolesController extends Controller
{

    /**
     * page permettant de lier des roles et des fonctions
     * @route("matching-fonctions", name="interne_roles_match_fonctions")
     */
    public function matchingFonctionsAction(Request $request) {

        $em         = $this->getDoctrine()->getManager();
        $roles      = $em->getRepository('InterneSecurityBundle:Role')->findAll();
        $fonctions  = $em->getRepository('AppBundle:Fonction')->findAll();

        if($request->request->get('matching-fonction-id') != null && $request->request->get('matching-linked-role') != null){ //On a un role à lier à une fonction

            $fonction = $em->getRepository('AppBundle:Fonction')->find($request->request->get('matching-fonction-id'));
            $role     = $em->getRepository('InterneSecurityBundle:Role')->find($request->request->get('matching-linked-role'));

            $fonction->addRole($role);
            $em->persist($fonction);
            $em->flush();

            return $this->redirect($this->generateUrl('interne_roles_match_fonctions'));
        }


        return $this->render('InterneSecurityBundle:Roles:page_matching_fonctions.html.twig', array(

            'roles'         => $roles,
            'fonctions'     => $fonctions,
        ));
    }

    /**
     * Retire un role d'une fonction
     * @param Fonction $fonction
     * @param Role $role
     * @route("unlink-fonction/{fonction}/{role}", name="interne_role_unlink_fonction", options={"expose"=true})
     * @paramConverter("role", class="InterneSecurityBundle:Role")
     * @paramConverter("fonction", class="AppBundle:Fonction")
     * @return Response
     */
    public function unlinkRoleFonction(Fonction $fonction, Role $role) {

        $em = $this->getDoctrine()->getManager();
        $fonction->removeRole($role);

        $em->persist($fonction);
        $em->flush();

        return $this->redirect($this->generateUrl('interne_roles_match_fonctions'));
    }
}