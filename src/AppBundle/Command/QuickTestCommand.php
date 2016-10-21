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

        /** @var UserRepository $uRepo */
        $uRepo = $this->getContainer()->get('app.repository.user');


        $u = $uRepo->find(1);

        $voter = new UserVoter();

       // dump($voter->supports('edit',$u));




    }


}


