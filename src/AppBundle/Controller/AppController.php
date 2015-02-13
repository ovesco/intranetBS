<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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
        $em = $this->getDoctrine()->getManager();
        $lastNews = $em->getRepository('InterneOrganisationBundle:News')->findForPaging(0,1);

        return $this->render('AppBundle:Homepage:page_homepage.html.twig', array('lastNews'=>$lastNews));
    }

    /**
     * @Route("test", name="interne_app_test")
     */
    public function testAction() {

        $groupe= $this->getDoctrine()->getManager()->getRepository('AppBundle:Groupe')->find(2);

        if($this->get('security.context')->isGranted('edit', $groupe)) {
            echo 'pass';
        }

        return new Response();
    }

}
