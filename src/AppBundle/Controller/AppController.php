<?php

namespace AppBundle\Controller;

use Interne\SecurityBundle\Entity\Role;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use AppBundle\Entity\Membre;

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
    public function homePageAction()
    {
        $em = $this->getDoctrine()->getManager();
        $lastNews = $em->getRepository('InterneOrganisationBundle:News')->findForPaging(0,1);

        /** @var Membre $membre */
        $membre = $this->getUser()->getMembre();
        $groupes = $membre->getActiveGroupes();




        return $this->render('AppBundle:Homepage:page_homepage.html.twig',
            array('lastNews'=>$lastNews, 'groupes'=>$groupes));
    }

    /**
     * @Route("test", name="interne_app_test")
     */
    public function testAction() {

        if($this->get('security.context')->isGranted('ROLE_FINANCES'))
            echo 'pass';

        return new Response('<body></body>');
    }

}
