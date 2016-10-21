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

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use AppBundle\Security\RoleHierarchy;


/**
 * Class UserPromoteCommand
 * @package AppBundle\Command
 */
class UserPromoteCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:user:promote')
            ->setDescription('add role to user')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'The user name'
            )
            ->addArgument(
                'role',
                InputArgument::REQUIRED,
                'the role to add'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output = new ConsoleOutput($output);

        $username = $input->getArgument('username');
        $role = $input->getArgument('role');

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        /** @var UserRepository $userRepo */
        $userRepo = $this->getContainer()->get('app.repository.user');

        $user = $userRepo->findOneBy(array('username'=>$username));

        /** @var RoleHierarchy $roleHierarchy */
        $roleHierarchy = $this->getContainer()->get('app.role.hierarchy');

        if($user instanceof User)
        {
            if($roleHierarchy->isExistingRole($role))
            {
                $user->addSelectedRoles($role);
                $userRepo->save($user);
                $output->info($username.' has been promoted to '.$role)->writeln();
            }
            else
            {
                $output->error('Not existing role: '.$role)->writeln();
            }
        }
        else
        {
            $output->error('Not found user: '.$username)->writeln();
        }


    }

}


