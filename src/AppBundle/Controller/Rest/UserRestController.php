<?php

namespace AppBundle\Controller\Rest;

use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @RouteResource("User")
 */
class UserRestController extends Controller
{

    public function getAction($id)
    {
        $user = $this->get('handler.user')->get($id);
        $userJson = $this->get('serializer.user')->serialize($user);
        return new Response($userJson);
    } // "get_user"      [GET] /users/{slug}

    public function newAction()
    {

    } // "new_users"     [GET] /users/new

}
