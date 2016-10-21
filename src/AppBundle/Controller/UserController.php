<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\User\UserType;

/* Annotation */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Utils\Menu\Menu;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Voters\UserVoter;

/**
 * @Route("/intranet/admin/user")
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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

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
        $this->denyAccessUnlessGranted('view',$user);

        return array('user'=>$user);
    }

    /**
     * @param Request $request
     * @Route("/create")
     * @Template("AppBundle:User:page_create.html.twig")
     * @return Response
     */
    public function createAction(Request $request){

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = new User();
        $userForm = $this->createForm(new UserType(),$user);

        $userForm->handleRequest($request);

        if($userForm->isValid())
        {
            $this->get('app.repository.user')->save($user);
            return $this->redirect($this->generateUrl('app_user_list'));
        }

        return array('form'=>$userForm->createView());

    }

    /**
     * @param Request $request
     * @Route("/edit/{user}")
     * @Template("AppBundle:User:page_edit.html.twig")
     * @return Response
     * @ParamConverter("user", class="AppBundle:User")
     */
    public function editAction(Request $request, User $user){

        $this->denyAccessUnlessGranted('edit',$user);

        $userForm = $this->createForm(new UserType(),$user);

        $userForm->handleRequest($request);

        if($userForm->isValid())
        {
            $this->get('app.repository.user')->save($user);
            return $this->redirect($this->generateUrl('app_user_list'));
        }

        return array('form'=>$userForm->createView());

    }

}