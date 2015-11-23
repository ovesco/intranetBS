<?php

namespace AppBundle\Command;

/* Specifics class for command */
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use AppBundle\Command\CustomOutput;

/**
 * L'idée de cette commande est de s'affichir de la confifuration
 * CRON du server (enfin de la simplifier).
 *
 *
 *
 *
 * Class CRONCommand
 * @package AppBundle\Command
 */
class CRONCommand extends ContainerAwareCommand
{

    const CRON_PATH = '/CRON/last_execution.txt';

    const MODE_DEFAULT = null;
    const MODE_RESET = 'reset';

    /** @var CustomOutput */
    private $output;
    private $input;

    private $now;

    private $tasks;

    protected function configure()
    {
        $this
            ->setName('cron')
            ->setDescription('Taches CRON sur l\'application')
            ->addArgument('mode', InputArgument::OPTIONAL, 'mode')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = new CustomOutput($output);
        $this->input = $input;

        $this->now = time();

        $this->tasks = array(
            new CRONTask('php app/console cache:clear',"40 seconds"),
            new CRONTask('php app/console route:naming:check',"40 seconds"),
        );

        $mode = $this->input->getArgument('mode');
        switch($mode)
        {
            case CRONCommand::MODE_DEFAULT:
                $this->load();
                $this->runTasks();
                $this->save();
                break;
            case CRONCommand::MODE_RESET:
                $this->save();
                break;
        }
        $this->output->yellowLabel('End CRON tasks (mode:'.$mode.')');
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

    protected function save()
    {
        $root = $this->getContainer()->getParameter('kernel.root_dir');
        $fs = new Filesystem();
        $fs->dumpFile($root.CRONCommand::CRON_PATH,json_encode($this->tasks));
    }

    protected function load()
    {
        $root = $this->getContainer()->getParameter('kernel.root_dir');
        $fs = new Filesystem();
        if($fs->exists($root.CRONCommand::CRON_PATH))
        {
            $content = file_get_contents($root.CRONCommand::CRON_PATH);
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

