<?php

namespace Interne\SecurityBundle\Tests\Voters;

use AppBundle\Entity\Membre;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Interne\SecurityBundle\Voters\FamilleVoter;
use Interne\SecurityBundle\Voters\MembreVoter;
use Interne\SecurityBundle\Voters\GroupeVoter;


class EntityVoter extends WebTestCase
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
     * Ici on teste si chaque voter d'entité sécurise bien les ressources.
     * On réalise plusieurs usecases par voter
     * Les tests réalisés ici nécessitent que la base de données soit correctement hydratée
     *
     * On récupère un membre donné ayant une famille contenant plusieurs membres et une attribution
     * de troupe. On peut ainsi tester les différents cas pour tous les voters
     */
    public function testAttribution() {

    }


}