<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Groupe;
use AppBundle\Entity\Membre;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Interne\SecurityBundle\Annotation\SecureResource;

/**
 * Class AppController
 * @package AppBundle\Controller
 */
class AppController extends Controller
{
    /**
     * Page d'accueil de l'application
     * @Route("", name="interne_homepage")
     *
     */
    public function indexAction()
    {
        return $this->render('App/homepage.html.twig');
    }


    /**
     * @SecureResource(role="ROLE_ADMIN", resource="membre")
     * @route("test/{membre}/{groupe}", name="test")
     * @paramConverter("membre", class="AppBundle:Membre")
     * @paramConverter("groupe", class="AppBundle:Groupe")
     */
    public function testAction(Membre $membre, Groupe $groupe) {

        return new Response('sss');
    }
}
