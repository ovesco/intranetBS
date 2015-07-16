<?php

namespace Interne\FinancesBundle\Entity;


class EntityTest extends \PHPUnit_Framework_TestCase
{
    public $creance;
    public $facture;
    public $payement;

    public $creanceTitre = 'Creance Test';
    public $creanceDate;
    public $creanceMontant = 1456.876;


    public function __construct()
    {
        date_default_timezone_set('Europe/Zurich');

        $this->creanceDate = new \DateTime();
        $this->creance = new Creance();
        $this->facture = new Facture();

    }
    public function testGetterSetterCreance()
    {

        $this->creance->setTitre($this->titre);
        $this->creance->setDateCreation($this->date);
        $this->creance->setMontantEmis($this->montant);
        $this->creance->setMontantRecu($this->montant);

        $this->assertEquals($this->creance->getTitre(), $this->titre);
        $this->assertEquals($this->creance->getDateCreation(), $this->date);
        $this->assertEquals($this->creance->getMontantEmis(), $this->montant);
        $this->assertEquals($this->creance->getMontantRecu(), $this->montant);
    }

    public function testGetterSetterFacture(){
        //$this->facture->setDateCreation();
        //$this->facture->setStatut();
    }


}