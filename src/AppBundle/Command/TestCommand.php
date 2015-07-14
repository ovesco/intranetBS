<?php

namespace AppBundle\Command;

/*
 * Specifics class for command
 */
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;



class TestCommand extends ContainerAwareCommand
{

    private $output;
    private $input;

    protected function configure()
    {
        $this
            ->setName('tests')
            ->setDescription('Tests sur l\'application')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->input = $input;

        $this->output('Starting tests','info');


        $version = shell_exec("php /usr/local/bin/phpunit --version");

        $this->output($version,'info');

        $message = shell_exec("php /usr/local/bin/phpunit -c app");

        $this->output($message,'comment');



        $pattern = '/OK/';
        $ok = preg_match($pattern,$message);
        $pattern = '/FAILURES/';
        $failures = preg_match($pattern,$message);
        if($ok && !$failures)
        {
            $this->output->writeln('<info>Test passed!!!</info>');
        }
        else
        {
            $this->output->writeln('<error>Error in tests...</error>');
        }




        $this->output('Finish tests','info');


    }


    private function doTest($path){

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


