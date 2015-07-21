<?php

namespace AppBundle\Test\Controller;

use AppBundle\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Client;
use Doctrine\ORM\EntityManager;

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
    public function testPageIsSuccessful($url)
    {
        $this->client->request('GET', $url);
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        echo PHP_EOL,'Testing route: '.$url,PHP_EOL;
    }

    public function urlProvider()
    {

        $this->setUp();

        /** @var EntityManager $em */
        $em = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        $urls = array();
        $urls = array_merge($urls,$this->urlAppController());
        $urls = array_merge($urls,$this->urlAttributionController($em));
        $urls = array_merge($urls,$this->urlBugReportController());
        $urls = array_merge($urls,$this->urlMembreController());
        $urls = array_merge($urls,$this->urlStructureController());
        $urls = array_merge($urls,$this->urlCategorieController($em));


        return $urls;
    }

    private function urlAppController()
    {
        return array(
            array('/interne'),
        );
    }

    private function urlAttributionController(EntityManager $em)
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

    private function urlBugReportController()
    {
        return array(
            array('/interne/bug_report/form'),
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

    private function urlCategorieController(EntityManager $em)
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