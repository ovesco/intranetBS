<?php

namespace AppBundle\Entity;


/**
 * Class EntityTest
 * @package AppBundle\Entity
 *
 * @group finances_bundle
 * @group entity_finances_bundle
 * @group entity
 */
class EntityTest extends \PHPUnit_Framework_TestCase
{
    public $creance;
    public $facture;
    public $payement;

    public $creanceTitre = 'Creance Test';
    public $creanceDate;
    public $creanceMontant = 1456.876;

    public $factureDate;
    public $factureStatut = Facture::OUVERTE;


    public function __construct()
    {
        date_default_timezone_set('Europe/Zurich');

        $this->creanceDate = new \DateTime();
        $this->factureDate = new \DateTime();



        $this->creance = $this->createCreance();
        $this->facture = $this->createFacture();

    }

    private function createCreance()
    {
        $c = new Creance();
        $c->setTitre($this->creanceTitre);
        $c->setDateCreation($this->creanceDate);
        $c->setMontantEmis($this->creanceMontant);
        $c->setMontantRecu($this->creanceMontant);

        return $c;
    }

    private function createFacture()
    {
        $f = new Facture();
        $f->setStatut($this->factureStatut);
        $f->setDateCreation($this->factureDate);

        return $f;
    }

    public function testGetterSetterCreance()
    {
        $this->assertEquals($this->creance->getTitre(), $this->creanceTitre);
        $this->assertEquals($this->creance->getDateCreation(), $this->creanceDate);
        $this->assertEquals($this->creance->getMontantEmis(), $this->creanceMontant);
        $this->assertEquals($this->creance->getMontantRecu(), $this->creanceMontant);
    }

    public function testGetterSetterFacture(){
        $this->assertEquals($this->facture->getStatut(), $this->factureStatut);
        $this->assertEquals($this->facture->getDateCreation(), $this->factureDate);
    }

    public function testEntityLink()
    {
        $f = clone $this->facture;
        $c = clone $this->creance;

        $f->addCreance($c);
        $this->assertEquals($f->getCreances()->first(), $c);


    }

}