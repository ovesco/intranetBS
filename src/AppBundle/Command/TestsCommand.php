<?php

namespace AppBundle\Command;

/* Specifics class for command */
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
/* Filesystem */
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;


class TestsCommand extends ContainerAwareCommand
{

    /** @var  ConsoleOutput */
    private $output;
    private $input;

    protected function configure()
    {
        $this
            ->setName('app:tests')
            ->setDescription('Tests sur l\'application')
            ->addArgument('group', InputArgument::OPTIONAL, 'group:groupeName')
            ->addOption('log',null,InputOption::VALUE_NONE,'log messages in file: app/logs/TestsCommand.log')
            ->addOption('clear_cache',null,InputOption::VALUE_NONE,"clear cache before tests");
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);

        $this->output = new ConsoleOutput($output);
        $this->input = $input;

        $groupArg = $this->input->getArgument('group');
        $group = null;
        if($groupArg != null)
        {
            $group = explode('group:',$groupArg)[1];
        }

        if ($input->getOption('clear_cache')) {
            //clear cache of env test
            $cache = shell_exec("php app/console cache:clear --env=test --no-debug");
            $this->output->info($cache,'info');
        }

        $startInfo = 'Starting tests';
        $shell_command = "php /usr/local/bin/phpunit -d memory_limit=-1 -c app";
        /* l'option "-d memory_limit=-1" permet de modifier la limite mémoire du script phpunit. */

        if($group != null)
        {
            $startInfo = $startInfo.' : group: '.$group;
            $shell_command = "php /usr/local/bin/phpunit --group ".$group." --verbose -d memory_limit=-1 -c app";
            /* l'option "-d memory_limit=-1" permet de modifier la limite mémoire du script phpunit. */
        }

        $this->output->info($startInfo);
        $version = shell_exec("php /usr/local/bin/phpunit --version");
        $this->output->info($version);


        $message = shell_exec($shell_command);
        $this->output->info($message);


        if ($input->getOption('log')) {
            $rootDir = $this->getContainer()->getParameter('kernel.root_dir');
            $logFile = $rootDir.'/logs/TestsCommand.log';
            $fs = new Filesystem();
            $fs->dumpFile($logFile,$message);
        }



        $pattern = '/OK/';
        $ok = preg_match($pattern,$message);
        $pattern = '/FAILURES/';
        $failures = preg_match($pattern,$message);
        if($ok && !$failures)
        {
            $this->output->success('Test passed!!!')->writeln();
            return 0;//travis exigence
        }
        else
        {
            $this->output->error('Error in tests...')->writeln();
            return 1;//travis exigence
        }

    }

}


