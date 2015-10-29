<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Membre;
use AppBundle\Utils\ListRenderer\ListContainer;
use AppBundle\Utils\ListRenderer\ListRenderer;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils\Menu\Menu;


class AppController extends Controller
{
    /**
     * Page d'accueil de l'application
     * @Route("", name="interne_homepage")
     * @Menu("home page", block="home block")
     */
    public function homePageAction()
    {
        $em = $this->getDoctrine()->getManager();
        $lastNews = $em->getRepository('InterneOrganisationBundle:News')->findForPaging(0, 1);

        return $this->render("AppBundle:Homepage:page_homepage.html.twig", array('lastNews' => $lastNews, 'user' => $this->getUser()));
    }

    /**
     * @route("test", name="test_menu")
     * @Menu("youyhouh",block="block test",order=1)
     */
    public function testAction()
    {
        return $this->render('AppBundle:Menu:menu_test.html.twig');
    }


    /**
     * @route("hello/{nom}", name="exemple_hello_word")
     * @Menu("coucou")
     */
    public function helloWordAction($nom)
    {
        return new Response("Salut les ".$nom);
    }

    /**
     * @route("test2")
     * @Menu(order=3)
     */
    public function test2() {

        $em = $this->getDoctrine()->getManager();
        $article = $em->find('AppBundle:Membre', 1 /*article id*/);
        $article->setPrenom('Guillaumeus');
        $em->persist($article);
        $em->flush();

        echo $article->getPrenom(); // prints "my title"

        return new Response();
    }

}
