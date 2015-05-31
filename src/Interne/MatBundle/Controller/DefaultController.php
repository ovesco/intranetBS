<?php

namespace Interne\MatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MatBundle:Default:index.html.twig', array('name' => $name));
    }
}
