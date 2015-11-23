<?php

namespace AppBundle\Tests\Routing;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class RoutingTester extends WebTestCase
{
    const APP_PREFIX = '/intranet';

    /** @var Client client */
    protected $client;

    /** @var EntityManager $em */
    protected $em;

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($route)
    {
        $this->client->request('GET', $route);
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Test route: ' . $route);
    }

    public function setUp()
    {
        /** @var Client client */
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'admin',
        ));


        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function urlProvider()
    {
        $this->setUp();

        return $this->getRoutes();
    }

    public function getRoutes(){ return array();}

}