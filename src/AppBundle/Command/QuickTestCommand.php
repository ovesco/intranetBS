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

use AppBundle\Entity\Facture;

class QuickTestCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('app:test:quick');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $repo = $this->getContainer()->get('app.repository.facture');


        /** @var Facture $f */
        foreach($repo->findAll() as $f)
        {
            $rand = random_int(0,100);
            if($rand <= 30)
                $f->setStatut(Facture::OPEN);
            elseif($rand <= 66)
                $f->setStatut(Facture::CANCELLED);
            else
                $f->setStatut(Facture::PAYED);

            $repo->save($f);
        }








    }


}


