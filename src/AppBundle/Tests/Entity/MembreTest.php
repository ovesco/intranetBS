<?php

namespace AppBundle\Test\Entity;

use AppBundle\Entity\Attribution;
use AppBundle\Entity\Distinction;
use AppBundle\Entity\Fonction;
use AppBundle\Entity\Groupe;
use AppBundle\Entity\Membre;

use AppBundle\Entity\ObtentionDistinction;
use AppBundle\Entity\Personne;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MembreTest extends WebTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @return Membre
     */
    public function addMember()
    {
        /** @var Membre $member */
        $member = $this->getTestMember();

        /* Add member */
        $this->em->persist($member);
        $this->em->flush();

        return $member;
    }

    public function removeMember($member)
    {
        /* Remove member and family */
        $this->em->remove($member);
        $this->em->remove($member->getFamille());
        $this->em->flush();
    }

    public function testAdd()
    {

        $member = $this->addMember();

        /** @var Membre $persistedMember */
        $persistedMember = $this->em->getRepository("AppBundle:Membre")->find($member);

        $this->assertEquals($member, $persistedMember);

    }

    public function testAddAttribution()
    {
        $member = $this->getTestMember();

        $attribution = new Attribution();
        $attribution->setDateDebut(new \DateTime("1989-04-01"));

        $function = new Fonction("Commandant");
        $attribution->setFonction($function);

        $group = new Groupe("BS");
        $attribution->setGroupe($group);

        $member->addAttribution($attribution);

        $this->em->persist($function);
        $this->em->persist($group);
        $this->em->persist($member);
        $this->em->flush();

        $persistedAttribution = $this->em->getRepository("AppBundle:Attribution")->find($attribution);
        $this->assertEquals($attribution, $persistedAttribution);

        $member->removeAttribution($attribution);
        $this->em->persist($member);
        $this->em->flush();
    }

    public function testAddDistinction()
    {
        $member = $this->getTestMember();

        $distinction = new Distinction("EMBS");
        $obtention = new ObtentionDistinction();
        $obtention->setDate(new \DateTime("2012-12-01"));
        $obtention->setDistinction($distinction);

        $member->addDistinction($obtention);

        $this->em->persist($distinction);
        $this->em->persist($member);
        $this->em->flush();

        $persistedDistinction = $this->em->getRepository("AppBundle:Distinction")->find($distinction);
        $this->assertEquals($distinction, $persistedDistinction);

        $member->removeDistinction($obtention);
        $this->em->persist($member);
        $this->em->flush();

    }

    public function testRemove()
    {
        $this->removeMember($this->getTestMember());
    }

    /**
     * Renvoie le membre de test BP
     *
     * @return Membre
     */
    static public function getTestMember()
    {
        $family = FamilleTest::getTestFamilly();

        $member = new Membre();

        $member->setPrenom("Charles");
        $member->setFamille($family);
        $member->setNumeroBs(7777);
        $member->setSexe(Personne::HOMME);

        return $member;
    }
}

?>