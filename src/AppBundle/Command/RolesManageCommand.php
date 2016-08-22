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
use AppBundle\Entity\Role;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


/**
 * Class RolesManageCommand
 * @package AppBundle\Command
 */
class RolesManageCommand extends ContainerAwareCommand
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
            ->setName('app:roles:manage')
            ->setDescription('')
            ->addArgument(
                'action',
                InputArgument::REQUIRED,
                'The action to do: add or remove'
            )
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'The user name'
            )
            ->addArgument(
                'role',
                InputArgument::REQUIRED,
                'the role to add or remove'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->customOutput = new ConsoleOutput($output);
        $this->output = $output;
        $this->input = $input;

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        switch ($input->getArgument('action')) {
            case 'add':
                $this->addRoleToUser($em, $input->getArgument('username'), $input->getArgument('role'));
                break;
            case 'remove':
                $this->removeRoleToUser($em, $input->getArgument('username'), $input->getArgument('role'));
                break;
        }

    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param $username
     * @param $roleName
     * @return mixed
     */
    private function addRoleToUser($em, $username, $roleName)
    {
        try {

            $user = $this->findUser($em, $username);
            $role = $this->findRole($em, $roleName);

        } catch (\Exception $e) {
            $this->customOutput->error($e->getMessage())->writeln();
            return null;
        }


        $user->addRole($role);
        $em->flush();
        $this->customOutput->greenLabel("Role " . $roleName . " added to user " . $username)->writeln();
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param $username
     * @param $roleName
     * @return mixed
     */
    private function removeRoleToUser($em, $username, $roleName)
    {
        try {

            $user = $this->findUser($em, $username);
            $role = $this->findRole($em, $roleName);

        } catch (\Exception $e) {
            $this->customOutput->error($e->getMessage())->writeln();
            return null;
        }

        $user->removeRole($role);
        $em->flush();
        $this->customOutput->greenLabel("Role " . $roleName . " remove to user " . $username)->writeln();
    }

    /**
     * @throws \Exception
     * @param \Doctrine\ORM\EntityManager $em
     * @param string $username
     * @return User
     */
    private function findUser($em, $username)
    {
        /** @var User $user */
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('username' => $username));
        //dans le cas ou l'on ne trouve pas le user
        if ($user == null) {
            throw new \Exception("No user with username: " . $username . " found.");
        }
        return $user;
    }

    /**
     * @throws \Exception
     * @param \Doctrine\ORM\EntityManager $em
     * @param string $roleName
     * @return Role
     */
    private function findRole($em, $roleName)
    {
        /** @var Role $role */
        $role = $em->getRepository('AppBundle:Role')->findOneBy(array('role' => $roleName));
        //dans le cas ou l'on ne trouve pas le role
        if ($role == null) {
            throw new \Exception("Role " . $roleName . " not found.");
        }
        return $role;
    }

}


