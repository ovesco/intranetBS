<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/* Annotation */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Utils\Menu\Menu;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * @Route("/interne/user")
 * @package AppBundle\Controller
 */
class UserController extends Controller
{
    /**
     * List of users
     *
     * @Route("/list", options={"expose"=true})
     * @param Request $request
     * @Menu("Liste des users", block="security", order=3, icon="users")
     * @Template("AppBundle:User:page_list.html.twig")
     * @return Response
     */
    public function listAction(Request $request)
    {
        return array();
    }


    /**
     * @Route("/show/{user}", options={"expose"=true})
     * @param Request $request
     * @ParamConverter("user", class="AppBundle:User")
     * @Template("AppBundle:User:page_show.html.twig")
     * @return Response
     */
    public function showAction(Request $request, User $user)
    {
        return array('user'=>$user);
    }

}