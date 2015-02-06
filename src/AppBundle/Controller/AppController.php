<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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
        return $this->render('AppBundle:Homepage:page_homepage.html.twig');
    }


    /**
     * @route("test", name="test")
     */
    public function testAction() {

        return new Response('');
    }
}
