<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Membre;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Utils\Menu\Menu;


/**
 * Class AppController
 * @package AppBundle\Controller
 * @Route("/intranet")
 */
class AppController extends Controller
{
    /**
     * Page d'accueil de l'application
     * @Route("")
     */
    public function homeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $lastNews = $em->getRepository('InterneOrganisationBundle:News')->findForPaging(0, 1);

        return $this->render("AppBundle:App:page_home.html.twig", array('lastNews' => $lastNews, 'user' => $this->getUser()));
    }

    /**
     * @Route("/test")
     * @Menu("Test",block="test",order=1)
     */
    public function testAction()
    {

        /** @var Membre $m */
        $m = $this->getDoctrine()->getRepository('AppBundle:Membre')->find(3);



        return $this->render('AppBundle:App:page_test.html.twig');
    }


}
