<?php

namespace Interne\FinancesBundle\Test\Controller;

use Interne\FinancesBundle\Entity\CreanceToMembre;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Client;
use Doctrine\ORM\EntityManager;

class RoutingTest extends WebTestCase
{

    /** @var Client client */
    private $client = null;

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
    }

    public function urlProvider()
    {

        $this->setUp();

        /** @var EntityManager $em */
        $em = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        $membre = $em->getRepository('AppBundle:Membre')->findOneBy(array());


        $creance = new CreanceToMembre();
        $creance->setTitre('WebTestCase');
        $creance->setDateCreation(new \DateTime());
        $creance->setMontantEmis('999.99');
        $creance->setMembre($membre);

        $em->persist($creance);
        $em->flush();

        $idCreance = $creance->getId();

        return array(
            array('/interne/finances/creance/search'),
            array('/interne/finances/creance/show/'.$idCreance),
            array('/interne/finances/creance/delete/'.$idCreance),
            array('/interne/finances/facture/search'),
            array('/interne/finances/facture/search'),
        );
    }

}