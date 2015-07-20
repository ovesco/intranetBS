<?php

namespace AppBundle\Test\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Client;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
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
    public function testPageIsSuccessful($url)
    {
        $this->client->request('GET', $url);
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        echo PHP_EOL,'Testing route: '.$url,PHP_EOL;
    }

    public function urlProvider()
    {

        $this->setUp();

        $urls = array();

        $urls = array_merge($urls,$this->urlBugReportController());
        $urls = array_merge($urls,$this->urlMembreController());
        $urls = array_merge($urls,$this->urlStructureController());

        return $urls;
    }

    private function urlBugReportController()
    {
        return array(
            array('interne/bug_report/form'),
        );
    }

    private function urlMembreController()
    {
        return array(
            array('/interne/membre/ajouter'),
        );
    }

    private function urlStructureController()
    {
        return array(
            array('/interne/structure/gestion_fonction'),
            array('/interne/structure/gestion_categorie'),
            array('/interne/structure/gestion_model'),
            //array('/interne/structure/gestion_groupe'),
        );
    }

}