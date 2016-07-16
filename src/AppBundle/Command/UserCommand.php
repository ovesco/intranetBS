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


/**
 * Class UserCommand
 * @package AppBundle\Command
 */
class UserCommand extends ContainerAwareCommand
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
            ->setName('app:user')
            ->setDescription('Permet de créer ou supprimer un user (non lié à un membre).')
            ->addArgument(
                'action',
                InputArgument::REQUIRED,
                'The actino to do: create or delete'
            )
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'The user name'
            )
            ->addArgument(
                'password',
                InputArgument::OPTIONAL,
                'the plain password (optional for suppression)'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->customOutput = new ConsoleOutput($output);
        $this->output = $output;
        $this->input = $input;

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        switch($input->getArgument('action'))
        {
            case 'create':
                $this->createUser($em,$input->getArgument('username'),$input->getArgument('password'));
                break;
            case 'delete':
                $this->deleteUser($em,$input->getArgument('username'));
                break;
        }

    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param $username
     * @param $password
     */
    private function createUser($em,$username,$password){

        $user = new User();
        $user->setUsername($username);
        $user->setPassword($password);
        $user->setLastConnexion(new \Datetime);

        $em->persist($user);
        $em->flush();

        $this->customOutput->info("New user created: ".$username)->writeln();
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param $username
     */
    private function deleteUser($em,$username){

        /** @var User $user */
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('username'=>$username));

        $em->remove($user);
        $em->flush();

        $this->customOutput->info("User removed.")->writeln();
    }



}


