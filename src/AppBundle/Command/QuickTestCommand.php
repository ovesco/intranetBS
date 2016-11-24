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

        $path = '/freu/we/{param}';

        //list($param,$i,$e) = $this->get_string_between($path,'{','}');

        $param = str_replace('{param}','1',$path);

        $output->writeln($param);




    }

    function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        $substring = substr($string, $ini, $len);
        return array($substring,$ini,$len);
    }


}


