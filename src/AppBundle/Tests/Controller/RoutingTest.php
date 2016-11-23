<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Categorie;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 *
 */
class RoutingTest extends WebTestCase
{

    /** @var Client client */
    private $client;

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($route)
    {
        $this->client->request('GET', $route);
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Test route: ' . $route);
    }

    public function urlProvider()
    {
        $this->setUp();

        /*

        /** @var EntityManager $em *
        $em = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        $membre = $em->getRepository('AppBundle:Membre')->findOneBy(array());
        $famille = $em->getRepository('AppBundle:Famille')->findOneBy(array());

        */
        $routes = array();
        $routes = array_merge($routes,$this->routeAppController());

        /*

        //$routes = array_merge($routes,$this->routeAttributionController($em));//error

        $routes = array_merge($routes,$this->routeBugReportController());//ok

        $routes = array_merge($routes,$this->routeMembreController()); //ok
        $routes = array_merge($routes,$this->routeStructureController()); //ok
        //$routes = array_merge($routes,$this->routeCategorieController($em)); //error
        $routes = array_merge($routes,$this->creanceRoute($em,$membre));
        $routes = array_merge($routes,$this->creanceRoute($em,$famille));
        $routes = array_merge($routes,$this->factureRoute($em,$membre));
        $routes = array_merge($routes,$this->factureRoute($em,$famille));
        $routes = array_merge($routes,$this->payementRoute($em));
        $routes = array_merge($routes,$this->debiteurRoute($membre));
        $routes = array_merge($routes,$this->debiteurRoute($famille));

*/

        return $routes;
    }

    public function setUp()
    {
        /** @var Client client */
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'admin',
        ));
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
/*
    private function creanceRoute(EntityManager $em, $ownerEntity){

        $creance = new Creance();
        $creance->setTitre('WebTestCase');
        $creance->setDateCreation(new \DateTime());
        $creance->setMontantEmis(999.99);
        $ownerEntity->getDebiteur()->addCreance($creance);

        $em->persist($creance);
        $em->flush();

        $idCreance = $creance->getId();


        return array(
            array('/interne/finances/creance/search'),
            array('/interne/finances/creance/show/'.$idCreance),
            array('/interne/finances/creance/delete/'.$idCreance),
        );
    }

    private function factureRoute(EntityManager $em, $ownerEntity){
        $facture = new Facture();
        $facture->setDateCreation(new \DateTime());

        $ownerEntity->getDebiteur()->addFacture($facture);

        $creanceForFacture = new Creance();
        $creanceForFacture->setTitre('WebTestCase');
        $creanceForFacture->setDateCreation(new \DateTime());
        $creanceForFacture->setMontantEmis(999.99);

        $ownerEntity->getDebiteur()->addCreance($creanceForFacture);
        $rappel = new Rappel();
        $rappel->setDateCreation(new \DateTime());
        $rappel->setMontantEmis(99.99);


        $facture->addCreance($creanceForFacture);
        $facture->addRappel($rappel);

        $em->persist($facture);
        $em->flush();

        $idFacture = $facture->getId();

        return array(
            array('/interne/finances/facture/search'),
            array('/interne/finances/facture/show/'.$idFacture),
            array('/interne/finances/facture/print/'.$idFacture),
            array('/interne/finances/facture/delete/'.$idFacture),
        );
    }

    private function payementRoute(EntityManager $em){

        $payement = new Payement();
        $payement->setDate(new \DateTime());
        $payement->setIdFacture(1243123);
        $payement->setMontantRecu(1234.23);
        $payement->setValidated(false);
        $payement->setState(Payement::NOT_FOUND);

        $em->persist($payement);
        $em->flush();

        $idPayement = $payement->getId();

        return array(
            array('/interne/finances/payement/search'),
            array('/interne/finances/payement/show/'.$idPayement),
            array('/interne/finances/payement/add'),
            array('/interne/finances/payement/validation'),
            array('/interne/finances/payement/validation_form/'.$idPayement),
            array('/interne/finances/payement/delete/'.$idPayement),
        );
    }

    private function debiteurRoute($ownerEntity)
    {
        $id = $ownerEntity->getDebiteur()->getId();
        return array(
            array('/interne/finances/debiteur/show/'.$id),
        );
    }
*/
}