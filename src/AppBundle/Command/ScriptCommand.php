<?php

namespace AppBundle\Command;

/* Specifics class for command */
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
/**
 * Cette commande permet de lancé des scripts. Chaque script est définit dans un
 * "switch case" qui prend en argument le nom du script voulu.
 *
 *
 * Class ScriptCommand
 * @package AppBundle\Command
 */
class ScriptCommand extends ContainerAwareCommand
{
    const SCRIPT_RESTART_DEV = 'restart_dev';
    const SCRIPT_RESTART_DATABASE = 'restart_database';


    /** @var  ConsoleOutput */
    protected $customOutput;
    /** @var InputInterface */
    protected $input;
    /** @var OutputInterface */
    protected $output;
    /** @var  ArrayCollection */
    protected $commands;

    protected function configure()
    {
        $this
            ->setName('app:script')
            ->setDescription('Permet d\'effectuer des script sur le projet. ')
            ->addArgument('script_name', InputArgument::REQUIRED, 'script_name: la liste des scripts disponible est dans le fichier ScriptCommand.php');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->customOutput = new ConsoleOutput($output);
        $this->output = $output;
        $this->input = $input;
        $this->commands = new ArrayCollection();

        $script = $this->input->getArgument('script_name');

        switch($script)
        {
            case 'restart_dev':
                $this->commands->add(new ConsoleCommand('cache:clear'));
                $this->commands->add(new ShellCommand('rm -rf '.$this->getContainer()->getParameter('upload_dir')));
                $this->commands->add(new ConsoleCommand('doctrine:database:drop',array('--force'=>true)));
                $this->commands->add(new ConsoleCommand('doctrine:database:create'));
                $this->commands->add(new ConsoleCommand('doctrine:schema:update',array('--force'=>true)));
                $this->commands->add(new ConsoleCommand('fos:elastica:reset'));
                //$this->commands->add(new ConsoleCommand('app:roles:build'));
                $this->commands->add(new ConsoleCommand('app:faker:populate'));
                $this->commands->add(new ConsoleCommand('app:user',array('action'=>'create','username'=>'admin','password'=>'admin')));
                $this->commands->add(new ConsoleCommand('app:user:promote',array('username'=>'admin','role'=>'ROLE_ADMIN')));
                $this->commands->add(new ConsoleCommand('fos:elastica:populate'));
                break;
            case 'restart_database':

                $this->commands->add(new ConsoleCommand('cache:clear'));
                $this->commands->add(new ShellCommand('rm -rf '.$this->getContainer()->getParameter('upload_dir')));

                /** @var AbstractSchemaManager $schemaManager */
                $schemaManager = $this->getContainer()->get('doctrine')->getConnection()->getSchemaManager();

                //on test juste une des tables pour voir si la base de donnée existe
                if ($schemaManager->tablesExist(array('app_users')) == true) {
                    // table exists! ...
                    $this->commands->add(new ConsoleCommand('doctrine:database:drop',array('--force'=>true)));
                    $this->commands->add(new ConsoleCommand('doctrine:database:create'));
                }

                $this->commands->add(new ConsoleCommand('doctrine:schema:update',array('--force'=>true)));
                $this->commands->add(new ConsoleCommand('fos:elastica:reset'));
                break;

            case 'populate_with_faker':

                $this->commands->add(new ConsoleCommand('app:faker:populate'));
                $this->commands->add(new ConsoleCommand('app:user',array('action'=>'create','username'=>'admin','password'=>'admin')));
                $this->commands->add(new ConsoleCommand('app:user:promote',array('username'=>'admin','role'=>'ROLE_ADMIN')));
                $this->commands->add(new ConsoleCommand('fos:elastica:reset'));
                $this->commands->add(new ConsoleCommand('fos:elastica:populate'));

                break;
        }

        $this->runScript();
    }

    /**
     * Execute la sucession de commande de chaque script.
     *
     * @return null
     */
    protected function runScript()
    {
        foreach($this->commands as $command)
        {
            if($command instanceof ConsoleCommand)
            {
                $console = $this->getApplication()->find($command->command);
                $input = new ArrayInput($command->args);
                $returnCode = $console->run($input, $this->output);
                if($returnCode != 0) {
                    $this->customOutput->error('Error with command: '.$command->command);
                    $this->customOutput->writeln();
                    return null;
                }
            }
            if($command instanceof ShellCommand)
            {
                $return = shell_exec($command->command);
                $this->customOutput->writeln($return);
            }
        }
    }


}



class ShellCommand
{
    public $command;

    public function __construct($command)
    {
        $this->command = $command;
    }
}

class ConsoleCommand
{
    public $command;
    public $args;

    public function __construct($command,$args = array())
    {
        $this->command = $command;
        $this->args = $args;
    }
}
