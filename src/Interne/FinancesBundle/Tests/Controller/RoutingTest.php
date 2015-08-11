<?php

namespace Interne\FinancesBundle\Test\Controller;

use Interne\FinancesBundle\Entity\Creance;
use Interne\FinancesBundle\Entity\Facture;
use Interne\FinancesBundle\Entity\Payement;
use Interne\FinancesBundle\Entity\Rappel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Client;
use Doctrine\ORM\EntityManager;

/**
 * Class RoutingTest
 * @package Interne\FinancesBundle\Test\Controller
 *
 * @group finances_bundle
 * @group routing_finances_bundle
 * @group routing
 */
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
        echo PHP_EOL,'Testing route: '.$url,PHP_EOL;
    }

    public function urlProvider()
    {

        $this->setUp();

        /** @var EntityManager $em */
        $em = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        $membre = $em->getRepository('AppBundle:Membre')->findOneBy(array());
        $famille = $em->getRepository('AppBundle:Famille')->findOneBy(array());

        $urls = array();

        $urls = array_merge($urls,$this->creanceUrl($em,$membre));
        $urls = array_merge($urls,$this->creanceUrl($em,$famille));
        $urls = array_merge($urls,$this->factureUrl($em,$membre));
        $urls = array_merge($urls,$this->factureUrl($em,$famille));
        $urls = array_merge($urls,$this->payementUrl($em));
        $urls = array_merge($urls,$this->debiteurUrl($membre));
        $urls = array_merge($urls,$this->debiteurUrl($famille));

        return $urls;
    }

    private function creanceUrl(EntityManager $em, $ownerEntity){

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

    private function factureUrl(EntityManager $em, $ownerEntity){
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

    private function payementUrl(EntityManager $em){

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

    private function debiteurUrl($ownerEntity)
    {
        $id = $ownerEntity->getDebiteur()->getId();
        return array(
            array('/interne/finances/debiteur/show/'.$id),
        );
    }

}