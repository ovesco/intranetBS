<?php

namespace AppBundle\Command;

/* Specifics class for command */
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
/* Filesystem */
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
/* Other */
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Parameter;

class ParameterCommand extends ContainerAwareCommand
{

    private $output;
    private $input;

    /** @var  EntityManager */
    private $em;

    protected function configure()
    {
        $this
            ->setName('parameters')
            ->setDescription('Gestion des parametres de l\'application')
            ->addArgument('mode', InputArgument::REQUIRED, 'mode: reset')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->input = $input;

        $mode = $this->input->getArgument('mode');

        switch($mode)
        {
            case 'reset':

                $this->em  = $this->getContainer()->get('doctrine.orm.entity_manager');

                /* Drop parameter table */
                $sql = 'DROP TABLE app_parameter;';
                $connection = $this->em->getConnection();
                $stmt = $connection->prepare($sql);
                $stmt->execute();
                $stmt->closeCursor();

                /* create table */
                $info = shell_exec("php app/console doctrine:schema:update --force");
                $this->output($info);


                $parameters = $this->getContainer()->getParameter('intranet_parameters');

                var_dump($parameters);


                foreach($parameters as $parameter_name => $parameter_def)
                {
                    /*
                     * Creation of each parameters in the list of "intranet_paramters"
                     */
                    $parameter = new Parameter();

                    $parameter->setName($parameter_name);

                    if(isset($parameter_def['type']))
                    {
                        $parameter->setType($parameter_def['type']);
                    }
                    else
                    {
                        throw new Exception("intranet_parameters with invalid type or undefined");
                    }
                    if(isset($parameter_def['options']))
                    {
                        $parameter->setOptions($parameter_def['options']);
                    }
                    $this->em->persist($parameter);
                }

                $this->em->flush();
                break;

        }





    }


    private function output($string,$mode = null){

        if (OutputInterface::VERBOSITY_VERBOSE <= $this->output->getVerbosity()) {

            switch($mode){
                case null;
                    break;
                case 'error':
                    $string = '<error>Error:</error> '.$string;
                    break;
                case 'info':
                    $string = '<info>Info:</info> '.$string;
                    break;
                case 'comment':
                    $string = '<comment>Comment:</comment> '.$string;
                    break;
            }
            $this->output->writeln(PHP_EOL.$string);
        }
    }

}


