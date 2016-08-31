<?php

namespace AppBundle\Controller\Rest;

use AppBundle\Entity\Membre;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

/**
 * @RouteResource("Membre")
 */
class MembreRestController extends Controller
{

    /**
     * @param Membre $membre
     * @return Response
     * @ParamConverter("membre", class="AppBundle:Membre")
     */
    public function getAction(Membre $membre)
    {

        $serializedMembre = $this->get('serializer.membre')->membreToJson($membre);
        return new Response($serializedMembre);
    } // "get_user"      [GET] /users/{slug}


    public function newAction()
    {

    } // "new_users"     [GET] /users/new

}
