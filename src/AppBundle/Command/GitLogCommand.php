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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 *
 * Doc sur git log -> https://git-scm.com/docs/git-log
 *
 * Class GitLogCommand
 * @package AppBundle\Command
 */
class GitLogCommand extends ContainerAwareCommand
{

    /** @var ConsoleOutput */
    protected $customOutput;

    /** @var InputInterface */
    protected $input;

    /** @var OutputInterface */
    protected $output;

    /** @var ArrayCollection */
    protected $fileLogs;

    /** @var string */
    protected $executionDir;

    protected function configure()
    {
        $this
            ->setName('app:git:log')
            ->setDescription('Classe les fichiers du projet par ordre de modification du git. Permet de rapidement ciblé les fichiers éroné ou depreacted')
            ->addOption('dir',null,InputOption::VALUE_REQUIRED,'Quel dossier à analyser?',"src")
            ->addOption('author',null,InputOption::VALUE_NONE,'If set, the author name is printed')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->customOutput = new ConsoleOutput($output);
        $this->output = $output;
        $this->input = $input;
        $this->fileLogs = new ArrayCollection();
        $this->executionDir = getcwd();//the root directory of the project

        $dir = $this->executionDir.'/'.$input->getOption('dir');

        $finder = new Finder();
        $finder->files()->in($dir);
        foreach ($finder as $file) {
            $fileName = $file->getRelativePathname();
            $this->fileLogs->add(new FileGitLog($dir.'/'.$fileName));
        }

        $this->usortByModifiedDate();
        $this->printLog();

    }

    /**
     * Reorganise les log en fonction de la date de modification
     */
    protected function usortByModifiedDate()
    {
        $iterator = $this->fileLogs->getIterator();
        $iterator->uasort(function (FileGitLog $a, FileGitLog $b) {
            return ($a->lastModifiedDate->getTimestamp() < $b->lastModifiedDate->getTimestamp()) ? -1 : 1;
        });
        $this->fileLogs = new ArrayCollection(iterator_to_array($iterator));
    }



    protected function printLog(){
        /** @var FileGitLog $filelog */
        foreach($this->fileLogs as $filelog)
        {
            $this->customOutput->write($filelog->lastModifiedDate->format('d.m.Y').' ');

            if($this->input->getOption('author'))
                $this->customOutput->info($filelog->author.' ');

            $relativePath = explode($this->executionDir,$filelog->path)[1];

            $this->customOutput->comment($relativePath);

            $this->customOutput->writeln();

        }
    }



}

class FileGitLog{

    /** @var \DateTime  */
    public $lastModifiedDate;

    /** @var string */
    public $path;

    /** @var string */
    public $author;

    /**
     * @param string $path
     */
    public function __construct($path){
        /*
         * Standard contruction
         */
        $this->lastModifiedDate = new \DateTime();
        $this->path = $path;
        $this->author = '';

        /*
         * Get git info
         */
        $separatorChar = "/";

        $unixTime = '%at';
        $author = '%an';

        $format = $unixTime.$separatorChar.$author.$separatorChar;

        $result =  shell_exec('git log -1 --format="'.$format.'" -- '.$this->path);

        $results = explode($separatorChar,$result);

        if(isset($results[0]))
        {
            if(($results[0] != '' )&& ($results[0] != null ))
            {
                $this->lastModifiedDate->setTimestamp(intval($results[0]));
            }
        }
        if(isset($results[1]))
        {
            if(($results[1] != '' )&& ($results[1] != null ))
            {
                $this->author = $results[1];
            }
        }

    }

}


