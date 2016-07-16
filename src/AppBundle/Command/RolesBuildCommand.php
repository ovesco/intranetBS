<?php

namespace AppBundle\Command;

/* Specifics class for command */
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use AppBundle\Entity\Role;

/**
 * Class RolesCommand
 * @package AppBundle\Command
 */
class RolesBuildCommand extends ContainerAwareCommand
{

    /** @var ConsoleOutput */
    protected $customOutput;

    /** @var InputInterface */
    protected $input;

    /** @var OutputInterface */
    protected $output;


    protected function configure()
    {
        $this
            ->setName('app:roles:build')
            ->setDescription('Build all roles hierarchy form the file liste_roles.yml');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->customOutput = new ConsoleOutput($output);
        $this->output = $output;
        $this->input = $input;

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->buildRoles($em);

    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @return mixed
     */
    private function buildRoles($em)
    {
        //on test d'abord si la liste de role à déjà été crée
        $roles = $em->getRepository("AppBundle:Role")->findAll();
        if (!empty($roles)) {
            $this->customOutput->error("Cannot build the role liste because the repository of roles is not empty.")->writeln();
            return null;
        }

        //on s'occupe maintenant de construire la hierarchie des roles a partir de la liste en parametre.
        $liste = $this->getContainer()->getParameter('liste_roles');

        $this->createRoleRecursively($liste, null, $em);
    }


    /**
     * @param $rolesInfos
     * @param Role $roleParent
     * @param \Doctrine\ORM\EntityManager $em
     */
    private function createRoleRecursively($rolesInfos, $roleParent, $em)
    {
        foreach ($rolesInfos as $roleName => $info) {

            $role = new Role();
            $role->setRole($roleName);

            if (!is_null($roleParent))
                $roleParent->addEnfant($role);

            if (isset($info['name']))
                $role->setName($info['name']);

            if (isset($info['description']))
                $role->setDescription($info['description']);


            if (isset($info['childs'])) {
                $this->createRoleRecursively($info['childs'], $role, $em);
            }
            $em->persist($role);
            $em->flush();
        }
    }
}


