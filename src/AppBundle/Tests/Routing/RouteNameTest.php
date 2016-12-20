<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 23.11.16
 * Time: 10:35
 */

namespace AppBundle\Tests\Routing;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Route;
use Doctrine\Common\Annotations\AnnotationReader as Reader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route as RouteAnnotation;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\SplFileInfo;



/**
 *
 * Cette class permet de tester la nomenclature de toutes les routes.
 *
 * Class RouteNameTest
 * @package AppBundle\Tests\Routing
 *
 * @group routing
 */
class RouteNameTest extends KernelTestCase{

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var RouterInterface
     */
    protected $router;

    /** @var ContainerInterface */
    private $container;


    /**
     * setUp is called automatically during instenciation...
     */
    public function setUp()
    {
        self::bootKernel();

        $this->container = self::$kernel->getContainer();

        /** @var Reader reader */
        $this->reader = $this->container->get('annotations.reader');

        /** @var RouterInterface $router */
        $this->router = $this->container->get('router');
    }


    /**
     * Cette fonction génere les données à tester
     *
     * @link https://phpunit.de/manual/current/en/writing-tests-for-phpunit.html#writing-tests-for-phpunit.data-providers
     */
    public function routesProvider()
    {
        $this->setUp();

        $data = array();

        foreach($this->findControllersClass() as $controllerClass)
        {

            $data = array_merge($data,$this->getControllerRoutes($controllerClass));
        }
        return $data;
    }


    /**
     * check si la route correspond au standard de nomencature
     *
     * @dataProvider routesProvider
     *
     * la méthode définie dans "dataProvider" permet d'injecter
     * les bon arguments dans le test de facon répétée. Ceci
     * permet de reseter avec la convention 1test = 1assertion.
     *
     */
    public function testRouteName(Route $route, $routeName)
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


        $this->assertTrue($computedName == $routeName,'Error in route name: '.$routeName. ' should be '.$computedName);
    }

    /**
     * This function return the class name of all the controllers
     * in the "src" directory.
     */
    public function findControllersClass()
    {
        $finder = new Finder();
        $srcDir = $this->container->get('kernel')->getRootDir() . "/../src";
        $finder->files()->in($srcDir)->name("*Controller.php");

        $controllersClass = array();
        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $fileName = $file->getRelativePathname();
            $className = str_replace('/','\\',$fileName);
            $controllersClass[] = str_replace('.php','',$className);
        }
        return $controllersClass;
    }


    /**
     * get route and route name of a controller
     *
     * @param string $controllerClass
     * @throws \InvalidArgumentException
     * @return array
     */
    public function getControllerRoutes($controllerClass)
    {
        if (!class_exists($controllerClass)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $controllerClass));
        }

        $class = new \ReflectionClass($controllerClass);
        if ($class->isAbstract()) {
            throw new \InvalidArgumentException(sprintf('Annotations from class "%s" cannot be read as it is abstract.', $controllerClass));
        }

        $data = array();
        foreach ($class->getMethods() as $method) {

            /** @var Route $route */
            foreach($this->router->getRouteCollection() as $routeName => $route)
            {
                $routeController = $route->getDefault('_controller');

                if($routeController == $controllerClass.'::'.$method->getName())
                {
                    $data[] = array($route,$routeName);
                }
            }
        }
        return $data;
    }

}