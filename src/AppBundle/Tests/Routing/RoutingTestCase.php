<?php

namespace AppBundle\Tests\Routing;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RoutingTestCase
 * @package AppBundle\Tests\Routing
 *
 * l'idée de cette class et de permettre de mettre en place rapidement
 * des tests sur toutes les routes d'un controller.
 *
 * Les tests seront lancés sur toutes les routes du controller
 * - execpté les route dans getExcludedRoutes()
 * - les routes nécessistant un parametre doivent etre résolue dans getParameters()
 *
 */
abstract class RoutingTestCase extends WebTestCase
{
    /** @var Client client */
    protected $client;

    /** @var  RouterInterface */
    protected $router;

    /** @var ContainerInterface */
    protected $container;

    /**
     * @dataProvider uriProvider
     * @link https://phpunit.de/manual/current/en/writing-tests-for-phpunit.html#writing-tests-for-phpunit.data-providers
     */
    public function testPageIsSuccessful($uri)
    {
        $this->client->request('GET', $uri);

        $this->logInFile($uri,$this->client->getResponse()->getContent());

        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Test route: ' . $uri);
    }

    public function setUp()
    {
        /** @var Client client */
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'admin',
        ));

        $this->router = self::$kernel->getContainer()->get('router');

        $this->container = self::$kernel->getContainer();
    }

    public function uriProvider()
    {
        $this->setUp();

        $uris = array();

        /** @var Route $route */
        foreach($this->getControllerRoutes() as $routeName => $route)
        {
            if(in_array($routeName,$this->getExcludedRoutes()))
                continue;

            $uris[] = array($this->replaceParamteterInPath($routeName,$route));
        }

        return $uris;
    }

    protected function replaceParamteterInPath($routeName,Route $route)
    {
        $path = $route->getPath();

        if(preg_match('/{\w*}+/' ,$path))
        {
            $parameters = $this->getParameters();

            $parameterForRoute = $parameters[$routeName];

            foreach($parameterForRoute as $parameter=>$value)
            {
                $path = str_replace('{'.$parameter.'}',$value,$path);
            }
        }


        return $path;
    }


    /**
     * @return array
     */
    abstract public function getParameters();

    /**
     *
     * cette fonction retourne la class du controller
     * à tester.
     *
     * @return string
     */
    abstract public function getControllerClass();

    /**
     * Return array of RouteName to exclude of tests
     *
     * @return array
     */
    abstract public function getExcludedRoutes();

    /**
     * get route and route name of a controller
     *
     *
     * @throws \InvalidArgumentException
     * @return array
     */
    public function getControllerRoutes()
    {
        $controllerClass = $this->getControllerClass();

        if (!class_exists($controllerClass)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $controllerClass));
        }

        $class = new \ReflectionClass($controllerClass);
        if ($class->isAbstract()) {
            throw new \InvalidArgumentException(sprintf('Annotations from class "%s" cannot be read as it is abstract.', $controllerClass));
        }

        $routes = array();
        foreach ($class->getMethods() as $method) {

            /** @var Route $route */
            foreach($this->router->getRouteCollection() as $routeName => $route)
            {
                $routeController = $route->getDefault('_controller');

                if($routeController == $controllerClass.'::'.$method->getName())
                {
                    $routes[$routeName] = $route;
                }
            }
        }
        return $routes;
    }

    /**
     * Dump un message d'erreur dans un fichier pour pouvoir plus
     * agréablement consulter l'erreur.
     *
     * @param $uri
     * @param $message
     */
    public function logInFile($uri,$message)
    {
        $file = str_replace ("/" ,  "_" ,  $uri  ).'.html';
        $webDir = self::$kernel->getContainer()->getParameter('kernel.root_dir').'/../web';
        $logFile = $webDir.'/test/log/page_tests/'.$file;
        $fs = new Filesystem();
        $fs->dumpFile($logFile,$message);
    }



}