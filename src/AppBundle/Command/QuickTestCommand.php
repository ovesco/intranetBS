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

use AppBundle\Repository\UserRepository;
use AppBundle\Voters\UserVoter;
use AppBundle\Entity\User;
use AppBundle\Security\RoleHierarchy;
use AppBundle\Security\RoleHierarchyBuilder;

class QuickTestCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('app:test:quick');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        /** @var RoleHierarchy $h */
        $h = $this->getContainer()->get('app.role.hierarchy');


        dump($h->getDeducedRoles(array('ROLE_CREANCE')));






    }


}


