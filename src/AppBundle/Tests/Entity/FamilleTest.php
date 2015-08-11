<?php

namespace AppBundle\Test\Entity;

use AppBundle\Entity\Contact;
use AppBundle\Entity\Email;
use AppBundle\Entity\Famille;
use AppBundle\Entity\Pere;
use AppBundle\Entity\Mere;
use AppBundle\Entity\Adresse;
use AppBundle\Entity\Telephone;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class FamilleTest
 * @package AppBundle\Test\Entity
 *
 * @group entity
 * @groupe app_bundle
 */
class FamilleTest extends WebTestCase
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


    public function testAdd()
    {

        $family = $this->getTestFamilly();

        $this->em->persist($family);
        $this->em->flush();

        /** @var Membre $persistedMember */
        $persistedFamily = $this->em->getRepository("AppBundle:Famille")->find($family);

        $this->assertEquals($family, $persistedFamily);

        /* Remove member */
        $this->em->remove($family);
        $this->em->flush();
    }

    /**
     * Renvoie la famille de test (de BP)
     *
     * @return Famille
     */
    static public function getTestFamilly()
    {
        $pere = new Pere();
        $pere->setPrenom("Georges");
        $pere->setProfession("Professeur de mathématiques");

        $mere = new Mere();
        $mere->setPrenom("Henriette");

        $contact = new Contact();
        $contact->setAdresse(new Adresse("29 Paddington St", "W1U 4HA", "London"));
        $contact->addEmail(new Email("bp@scouts.com"));
        $contact->addTelephone(new Telephone("021 782 56 74"));

        $famille = new Famille();

        $famille->setNom("Powell");
        $famille->setPere($pere);
        $famille->setMere($mere);
        $famille->setContact($contact);

        return $famille;
    }
}

?>