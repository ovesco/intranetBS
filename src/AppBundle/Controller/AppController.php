<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AppController extends Controller
{
    /**
     * Page d'accueil de l'application
     * @Route("/interne", name="interne_homepage")
     */
    public function indexAction()
    {
        return $this->render('App/homepage.html.twig');
    }


    /**
     * @route("test", name="test")
     */
    public function testAction() {

        $validation = $this->get('validation');
        $validation->validateField('yolo', 'famille.1.pere.prenom');

        return new Response('');
    }
}
