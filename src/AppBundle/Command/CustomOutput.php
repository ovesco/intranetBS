<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Form\Form;


class Color{
    const NONE = '';
    const BLUE = 'blue';
    const WHITE = 'white';
    const YELLOW = 'yellow';
    const BLACK = 'black';
    const GREEN = 'green';
}

class Format{
    const NONE = '';
    const BOLD = 'bold';
    const UNDERSCORE = 'underscore';
}

class Mode{
    const STANDARD = '';
    const ERROR = 'error';
    const INFO = 'info';
    const COMMENT = 'comment';
}

/**
 * doc: http://symfony.com/doc/current/components/console/introduction.html
 *
 * Avaliable colors: black, red, green, yellow, blue, magenta, cyan, white.
 *
 * Avaliable formats: bold, underscore, blink, reverse.
 *
 *
 * verbosity: VERBOSITY_DEBUG > VERBOSITY_VERY_VERBOSE > VERBOSITY_VERBOSE > VERBOSITY_NORMAL
 *
 *
 * Class CustomOutput
 * @package AppBundle\Command
 */
class CustomOutput
{
    /** @var OutputInterface  */
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function write($string = null)
    {
        if(is_null($string))
            $string = ' ';
        $this->output->write($string);
    }

    public function writeln($string = null)
    {
        if(is_null($string))
            $string = ' ';
        $this->output->writeln($string);
    }

    public function writeMode($string = null,$mode = Mode::STANDARD){

        if (OutputInterface::VERBOSITY_VERBOSE <= $this->output->getVerbosity()) {

            switch($mode){
                case Mode::STANDARD:
                    //do nothing
                    break;
                case Mode::ERROR:
                    $string = '<error>'.$string.'</error> ';
                    break;
                case Mode::INFO:
                    $string = '<info>'.$string.'</info> ';
                    break;
                case Mode::COMMENT:
                    $string = '<comment>'.$string.'</comment> ';
                    break;
            }
            $this->output->write($string);
        }
    }

    public function writeCustom($string,$colorBackground = Color::NONE,$colorFont = Color::NONE,$format = Format::NONE)
    {
        $this->output->write('<fg='.$colorFont.';bg='.$colorBackground.';options='.$format.'>'.$string.'</>');
    }

    public function blueLabel($string)
    {
        $this->writeCustom($string,Color::BLUE,Color::WHITE);
    }

    public function yellowLabel($string)
    {
        $this->writeCustom($string,Color::YELLOW,Color::BLACK);
    }

    public function greenLabel($string)
    {
        $this->writeCustom($string,Color::GREEN,Color::WHITE);
    }

    public function error($string){
        $this->writeMode($string,Mode::ERROR);
        $this->writeln();
    }

}