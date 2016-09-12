<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Fonction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/* Annotation */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Utils\Menu\Menu;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/intranet/admin/role")
 * @package AppBundle\Controller
 */
class RoleController extends Controller
{
    /**
     * Page qui affiche la hierarchie des roles
     *
     * @Route("/list", options={"expose"=true})
     * @param Request $request
     *
     * @Menu("Liste des roles", block="security", order=2, icon="list")
     * @Template("AppBundle:Roles:page_list.html.twig")
     * @return Response
     */
    public function listAction(Request $request)
    {
        $em         = $this->getDoctrine()->getManager();
        $roles      = $em->getRepository('AppBundle:Role')->findby(array('parent'=>null));

        return array('rootRoles'=>$roles);
    }

}