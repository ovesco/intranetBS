<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GroupeController extends Controller
{

    /**
     * @param $groupe Groupe le groupe
     * @return Response la vue
     *
     * @paramConverter("groupe", class="AppBundle:Groupe")
     * @route("groupe/voir-groupe/{groupe}", name="interne_voir_groupe", options={"expose"=true})
     * @Template("Groupe/voir_groupe.html.twig", vars={"groupe"})
     */
    public function showGroupeAction($groupe) {

        return array(
            'listing' => $this->get('listing'),
            'groupe'  => $groupe
        );
    }
}