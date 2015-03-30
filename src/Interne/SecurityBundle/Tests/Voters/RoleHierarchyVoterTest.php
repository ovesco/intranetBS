<?php

namespace Interne\SecurityBundle\Tests\Voters;

use Interne\SecurityBundle\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Interne\SecurityBundle\Voters\RoleHierarchyVoter;

class RoleHierarchyVoterTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $session;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->session = static::$kernel->getContainer()->get("session");
    }

    /**
     * Le but de la méthode fetchRoles est d'applatir la hierarchie de roles d'un utilisateur
     * Pour tester cela, on récupère le role admin, et on vérifie que sa hierarchie soit bien applatie
     */
    public function testFetchRoles() {

        $voter = new RoleHierarchyVoter($this->em, $this->session);

        $r1 = $this->createRole();
        $r2 = $this->createRole();
        $r3 = $this->createRole();
        $r4 = $this->createRole();
        $r5 = $this->createRole();
        $r6 = $this->createRole();
        $r7 = $this->createRole();
        $r8 = $this->createRole();
        $r9 = $this->createRole();

        $r1->addEnfant($r2);
        $r1->addEnfant($r3);

        $r3->addEnfant($r4);

        $r4->addEnfant($r5);
        $r4->addEnfant($r6);

        $r7->addEnfant($r8);
        $r8->addEnfant($r9);

        $roles = array($r1, $r7);
        $roles = $voter->fetchRoles($roles);

        $this->assertEquals(9, count($roles));
        $this->assertContains($r4, $roles);
        $this->assertContains($r6, $roles);
    }

    private function createRole() {

        $role = new Role();
        $nbr  = mt_rand(1,500);
        $role->setRole("ROLE_" . $nbr);
        $role->setName("role " . $nbr);
        $role->setDescription("description du role " . $nbr);

        return $role;
    }

}