<?php

namespace Interne\SecurityBundle\Command;

use Interne\SecurityBundle\Entity\Role;
use Interne\SecurityBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;



class SetRoleAdminToUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('security:set:admin')
            ->setDescription("Give the role admin to a user")
            ->addArgument('username', InputArgument::REQUIRED, 'Username');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        /** @var \Doctrine\ORM\EntityManager $em */
        $em         = $this->getContainer()->get('doctrine.orm.entity_manager');
        $username   = $input->getArgument('username');


        /** @var User $user */
        $user = $em->getRepository('InterneSecurityBundle:User')->findOneByUsername($username);


        /** @var Role $role */
        $role = $em->getRepository('InterneSecurityBundle:Role')->find(1); //role admin

        $user->addRole($role);



        $em->persist($user);

        $em->flush();
    }



}