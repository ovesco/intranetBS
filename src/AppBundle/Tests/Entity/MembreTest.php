<?php

namespace AppBundle\Test\Entity;

use AppBundle\Entity\Membre;

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

    public function testAdd()  {

        /** @var Membre $member */
        $member = $this->getTestMember();

        /* Add member */
        $this->em->persist($member);
        $this->em->flush();

        /** @var Membre $persistedMember */
        $persistedMember = $this->em->getRepository("AppBundle:Membre")->find($member);

        $this->assertEquals($member, $persistedMember);

        /* Remove member and family */
        $this->em->remove($member);
        $this->em->remove($member->getFamille());
        $this->em->flush();
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