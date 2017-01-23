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

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Route;

use Doctrine\Common\Annotations\AnnotationReader as Reader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route as RouteAnnotation;
use AppBundle\Tests\Routing\RoutingTestCase;

class RouteCheckCommand extends ContainerAwareCommand
{

    /**
     * @var ConsoleOutput
     */
    protected $customOutput;

    /** @var InputInterface */
    protected $input;

    /** @var OutputInterface */
    protected $output;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var RouterInterface
     */
    protected $router;


    protected function configure()
    {
        $this
            ->setName('app:route:check')
            ->setDescription('Effectue un check sur la nomencalture de l\'ensemble des route de l\'application')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);//set debug mode

        $this->customOutput = new ConsoleOutput($output);
        $this->output = $output;
        $this->input = $input;

        /** @var Reader reader */
        $this->reader = $this->getContainer()->get('annotations.reader');

        /** @var RouterInterface $router */
        $this->router = $this->getContainer()->get('router');

        $controllersClass = $this->findControllersClass();
        foreach($controllersClass as $controllerClass)
        {
            $this->checkController($controllerClass);
        }

    }


    /**
     * check si la route correspond au standard de nomencature
     *
     * @param Route $route
     * @param $routeName
     * @param boolean $tested
     */
    protected function checkRoute(Route $route,$routeName,$tested,$showTestedInfo)
    {
        $controllerAction = $route->getDefault('_controller');

        /*
         * Correspond à la nomenclature par défaut que symfony
         * donne à chaque route qui ne définit pas son nom.
         */
        $computedName = str_replace('\\Controller','',$controllerAction);
        $computedName = str_replace('Controller','',$computedName);
        $computedName = str_replace('Bundle','',$computedName);
        $computedName = str_replace('Action','',$computedName);
        $computedName = str_replace('::','_',$computedName);
        $computedName = strtolower($computedName);
        $computedName = str_replace('\\','_',$computedName);

        if($showTestedInfo)
        {
            if($tested)
            {
                $this->customOutput->greenLabel(' Test:Tested ');
            }
            else
            {
                $this->customOutput->writeMode(' Test:Excluded ',Mode::ERROR);
            }
        }

        if($computedName == $routeName)
        {
            $this->customOutput->greenLabel(' Name:ok ');
            $this->customOutput->writeMode(' '.$computedName,Mode::INFO);
            $this->customOutput->writeln();
        }
        else
        {
            $this->customOutput->writeMode(' Name:error ',Mode::ERROR);
            $this->customOutput->write($routeName);
            $this->customOutput->writeMode(' should be: ',Mode::COMMENT);
            $this->customOutput->writeln($computedName);
        }
    }

    /**
     * This function return the class name of all the controllers
     * in the "src" directory.
     */
    protected function findControllersClass()
    {
        $finder = new Finder();
        $srcDir = $this->getContainer()->get('kernel')->getRootDir() . "/../src";
        $finder->files()->in($srcDir)->name("*Controller.php");

        $controllersClass = array();
        foreach ($finder as $file) {
            $fileName = $file->getRelativePathname();
            $className = str_replace('/','\\',$fileName);
            $controllersClass[] = str_replace('.php','',$className);
        }
        return $controllersClass;
    }


    /**
     * @param string $controllerClass
     * @throws \InvalidArgumentException
     */
    protected function checkController($controllerClass)
    {
        if (!class_exists($controllerClass)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $controllerClass));
        }

        $class = new \ReflectionClass($controllerClass);
        if ($class->isAbstract()) {
            throw new \InvalidArgumentException(sprintf('Annotations from class "%s" cannot be read as it is abstract.', $controllerClass));
        }

        $this->customOutput->comment('Controller: ')->info($controllerClass)->writeln();

        $controllerTestClass = $this->findControllersTestClass($class->getShortName());
        /** @var RoutingTestCase $routingTestCase */
        $routingTestCase = null;
        $testedController = false;

        if(!is_null($controllerTestClass))
        {
            $routingTestCase = new $controllerTestClass();
            $testedController = true;
            $this->customOutput->greenLabel('Tested: ')->info($controllerTestClass)->writeln();
        }
        else
        {
            $this->customOutput->error('Not tested...')->writeln();
        }




        foreach ($class->getMethods() as $method) {

            /** @var Route $route */
            foreach($this->router->getRouteCollection() as $routeName => $route)
            {
                $routeController = $route->getDefault('_controller');

                if($routeController == $controllerClass.'::'.$method->getName())
                {
                    $testedRoute = false;
                    if($testedController)
                    {
                        if(!in_array($routeName,$routingTestCase->getExcludedRoutes()))
                        {
                            $testedRoute = true;
                        }
                    }
                    $this->checkRoute($route,$routeName,$testedRoute,$testedController);
                }
            }
        }
        //space before next controller
        $this->customOutput->writeln();
    }


    /**
     * This function return the class name of all the controllers
     * in the "src" directory.
     *
     * @param $controllerClassName (for exemple: AppController)
     * @return string
     * @throws \Exception
     */
    protected function findControllersTestClass($controllerClassName)
    {
        $testClass = $controllerClassName."RoutingTest.php";
        $finder = new Finder();
        $testDir = $this->getContainer()->get('kernel')->getRootDir() . "/../src/";
        $finder->files()->in($testDir)->name($testClass);

        $controllersClass = array();
        foreach ($finder as $file) {
            $fileName = $file->getRelativePathname();
            $className = str_replace('/','\\',$fileName);
            $controllersClass[] = str_replace('.php','',$className);
        }
        if(count($controllersClass) > 1)
            throw new \Exception('Should have only one test class by controllers');

        if(!isset($controllersClass[0]))
            return null;

        return $controllersClass[0];
    }

}


