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

use AppBundle\Utils\Security\RoleHierarchy;


class QuickTestCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('app:test:quick');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        /** @var \Doctrine\ORM\EntityManager $em */
       // $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $liste = $this->getContainer()->getParameter('role_hierarchy');

        //dump($liste);

        $role = $this->getContainer()->get('role.hierarchy');

        $r = $role->getDeductedRoles('ROLE_CREANCE');

        //dump($r);





    }


}


