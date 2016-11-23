<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;


class GeekStatCommand extends ContainerAwareCommand
{

    /** @var ConsoleOutput */
    protected $customOutput;

    /** @var InputInterface */
    protected $input;

    /** @var OutputInterface */
    protected $output;


    protected function configure()
    {
        $this
            ->setName('app:geek:stat')
            ->setDescription('Calcule quelques petites statistique de geek pour rire')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->customOutput = new ConsoleOutput($output);
        $this->output = $output;
        $this->input = $input;

        $finder = new Finder();
        $srcDir = $this->getContainer()->get('kernel')->getRootDir() . "/../src";
        $finder->files()->in($srcDir);

        $counter = 0;

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $fileName = $file->getRelativePathname();
            if($file->isFile())
            {
                $count = $this->countNumberOfCodeLine($file);
                $this->customOutput->write($fileName.": ")->info($count)->writeln("");
                $counter = $counter + $count;
            }
        }
        $this->customOutput->greenLabel('Total code lines: '.$counter)->writeln();
    }

    /**
     * @param SplFileInfo $file
     * @return int
     */
    private function countNumberOfCodeLine(SplFileInfo $file)
    {
        if($file->isFile())
        {
            $counter = 0;
            $lines = file($file);
            foreach($lines as $line)
            {
                if($line != PHP_EOL)
                {
                    $counter++;
                }
            }
            return $counter;
        }
        return 0;
    }
}


