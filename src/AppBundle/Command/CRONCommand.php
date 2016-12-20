<?php

namespace AppBundle\Command;

/* Specifics class for command */
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * L'idée de cette commande est de s'affichir de la confifuration
 * CRON du server (enfin de la simplifier).
 *
 * Donc plus qu'à faire tourner qu'une seul commande avec CRON (celle-ci)
 * à une fréquence suffisante.
 *
 * Class CRONCommand
 * @package AppBundle\Command
 */
class CRONCommand extends ContainerAwareCommand
{

    /** @var ConsoleOutput */
    private $output;
    /** @var InputInterface */
    private $input;

    /** @var Integer Unix timestamp */
    private $now;

    /** @var array Liste des tâches CRON */
    private $tasks;


    protected function configure()
    {
        $this
            ->setName('app:cron')
            ->setDescription('Taches CRON sur l\'application')
            ->addOption('reset',null,InputOption::VALUE_NONE,'reset allow to restart cron tasks based on the current time')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = new ConsoleOutput($output);
        $this->input = $input;
        $this->now = time();


        /*
         * LISTE DES TACHES CRON
         */
        $this->tasks = array(
            new CRONTask('php app/console cache:clear',"20 seconds"),//todo NUR: changer si passage en mode prop => to 1 day
            new CRONTask('php app/console swiftmailer:spool:send --env=dev',"1 day"),//todo NUR: changer si passage en mode prod => env=prod
        );


        if(!$this->input->getOption('reset'))
        {
            //if option --reset is not set
            $this->load();
            $this->runTasks();
        }

        $this->save();

        $this->output->yellowLabel('End CRON tasks');
        $this->output->writeln();
    }

    protected function runTasks()
    {
        /** @var CRONTask $task */
        foreach($this->tasks as $key=>$task)
        {
            /** @var \DateInterval $interval */
            $interval = \DateInterval::createFromDateString($task->interval);

            $nextExexution = new \DateTime();
            $nextExexution->setTimestamp($task->lastExecution);
            $nextExexution->add($interval);

            if($this->now >= $nextExexution->getTimestamp())
            {
                $this->output->writeln('Execute: '.$task->command);
                shell_exec($task->command);
                $task->lastExecution = $this->now;
                $this->tasks[$key] = $task;
            }
        }
    }

    /**
     * save time of execution in file
     */
    protected function save()
    {
        $file = $this->getContainer()->getParameter('cron_execution_time_file');
        $fs = new Filesystem();
        $fs->dumpFile($file,json_encode($this->tasks));
    }

    /**
     * load last execution time from file
     */
    protected function load()
    {
        $file = $this->getContainer()->getParameter('cron_execution_time_file');
        $fs = new Filesystem();
        if($fs->exists($file))
        {
            $content = file_get_contents($file);
            $this->tasks = json_decode($content);
        }
    }
}

class CRONTask
{
    public function __construct($command,$interval,$lastExecution = null){
        $this->command = $command;
        $this->interval = $interval;
        if($lastExecution == null)
            $this->lastExecution = time();
        else
            $this->lastExecution = $lastExecution;
    }

    /** @var  string */
    public $command;

    /** @var integer */
    public $lastExecution;

    /** @var string */
    public $interval;
}

