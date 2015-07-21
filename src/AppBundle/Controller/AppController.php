<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;

use AppBundle\Utils\ListRender\ListContainer;
use AppBundle\Utils\ListRender\Column;

/**
 * Class AppController
 * @package AppBundle\Controller
 */
class AppController extends Controller
{
    /**
     * Page d'accueil de l'application
     * @Route("", name="interne_homepage")
     */
    public function homePageAction()
    {
        $em = $this->getDoctrine()->getManager();
        $lastNews = $em->getRepository('InterneOrganisationBundle:News')->findForPaging(0,1);

        return $this->render("AppBundle:Homepage:page_homepage.html.twig", array('lastNews' => $lastNews, 'user' => $this->getUser()));
    }

    /**
     * @route("test")
     */
    public function test() {

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $objs = $em->getRepository('AppBundle:Membre')->findBy(array(),array(),5);

        /** @var ListContainer $container */
        $container = $this->get('list_container');

        $list = $container->getNewListRender();
        $list->setObjects($objs);
        $list->setName('un_nom');
        $list->setSearchBar(true);


        $col = new Column('Id',function($obj){return $obj->getId();});
        $list->addColumn($col);
        $col = new Column('Prenom',function($obj){return $obj->getPrenom();},'capitalize|lower');
        $list->addColumn($col);



        return new Response($list->render());
    }




}
