<?php

namespace Interne\FinancesBundle\Entity;


use Interne\FinancesBundle\Entity\Creance;

class CreanceTest extends \PHPUnit_Framework_TestCase
{

    public function __construct()
    {
        date_default_timezone_set('Europe/Zurich');
    }
    public function testGetterSetter()
    {

        $titre = 'CreanceToMembre';
        $date = new \DateTime();
        $montant = 234.234;
        $creanceToMembre = new CreanceToMembre();
        $creanceToMembre->setTitre($titre);
        $creanceToMembre->setDateCreation($date);
        $creanceToMembre->setMontantEmis($montant);
        $creanceToMembre->setMontantRecu($montant);

        $this->assertEquals($creanceToMembre->getTitre(), $titre);
        $this->assertEquals($creanceToMembre->getDateCreation(), $date);
        $this->assertEquals($creanceToMembre->getMontantEmis(), $montant);
        $this->assertEquals($creanceToMembre->getMontantRecu(), $montant);
    }


}