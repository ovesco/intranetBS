<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

        $generator = $this->get('PDFGenerator');
        $MManager  = $generator->getModelsManager();

        $model     = $MManager->loadModel('liste_de_troupe');

        echo $model->templateDir;



        return new Response('');
    }
}
