<?php

namespace AppBundle\Controller;

use AppBundle\Security\RoleHierarchy;
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
     * Modal qui montre la hierachie des roles
     *
     * @Route("/hierarchy", options={"expose"=true})
     * @param Request $request
     * @Template("AppBundle:Roles:modal_hierarchy.html.twig")
     * @return Response
     */
    public function hierarchyAction(Request $request)
    {
        /** @var RoleHierarchy $hierarchyService */
        $hierarchyService = $this->get('app.role.hierarchy');

        return array('hierarchy'=>$hierarchyService->getHierarchy());
    }

}