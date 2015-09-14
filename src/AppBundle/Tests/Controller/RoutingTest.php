<?php

namespace AppBundle\Test\Controller;

use AppBundle\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Client;
use Doctrine\ORM\EntityManager;

/**
 * @group app_bundle
 * @group routing_app_bundle
 * @group routing
 */
class RoutingTest extends WebTestCase
{

    /** @var Client client */
    private $client;

    public function setUp()
    {
        /** @var Client client */
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ));
    }

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($route)
    {

        //echo PHP_EOL,'start testing route: '.$route,PHP_EOL;
        $this->client->request('GET', $route);
        $this->assertTrue($this->client->getResponse()->isSuccessful(),'Test route: '.$route);
        //echo PHP_EOL,'end testing route: '.$route,PHP_EOL;
    }

    public function urlProvider()
    {

        $this->setUp();

        /** @var EntityManager $em */
        $em = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        $routes = array();
        //$routes = array_merge($routes,$this->routeAppController());

        //$routes = array_merge($routes,$this->routeAttributionController($em));//error

        $routes = array_merge($routes,$this->routeBugReportController());//ok

        $routes = array_merge($routes,$this->routeMembreController()); //ok
        $routes = array_merge($routes,$this->routeStructureController()); //ok
        //$routes = array_merge($routes,$this->routeCategorieController($em)); //error



        return $routes;
    }

    private function routeAppController()
    {
        return array(
            array('/interne'),
        );
    }

    private function routeAttributionController(EntityManager $em)
    {

        $attribution = $em->getRepository('AppBundle:Attribution')->findOneBy(array());
        $idAttribution = $attribution->getId();

        $membre = $em->getRepository('AppBundle:Membre')->findOneBy(array());
        $idMembre = $membre->getId();

        $date = new \DateTime();

        return array(
            array('/interne/attribution/modal-or-persist'),
            array('/interne/attribution/render-form'),
            //array('/interne/attribution/terminer-attributions'), //nécésite POST request
            array('/interne/attribution/modal/edit/'.$idAttribution),
            array('/interne/attribution/modal/terminate/'.$idAttribution.'/'.$date->getTimestamp()),
            array('/interne/attribution/modal/add'),
            array('/interne/attribution/modal/add/'.$idMembre),
            array('/interne/attribution/add'),
            //array('/interne/attribution/add-multimembre'), //still error class not found
            array('/interne/attribution/edit/'.$idAttribution),
            array('/interne/attribution/add'),
            array('/interne/attribution/remove/'.$idAttribution),


        );
    }

    private function routeBugReportController()
    {
        return array(
            array('/interne/bug_report/form'),
        );
    }

    private function routeMembreController()
    {
        return array(
            array('/interne/membre/ajouter'),
        );
    }

    private function routeStructureController()
    {
        return array(
            array('/interne/structure/gestion_fonction'),
            array('/interne/structure/gestion_model'),
            //array('/interne/structure/gestion_groupe'),
        );
    }

    private function routeCategorieController(EntityManager $em)
    {
        $categorie = new Categorie('Webtestcase');
        $em->persist($categorie);
        $em->flush();
        $id = $categorie->getId();

        return array(
            array('/interne/categorie/new'),
            array('/interne/categorie/liste'),
            array('/interne/categorie/edit/'.$id),
            array('/interne/categorie/remove/'.$id)
        );

    }

}