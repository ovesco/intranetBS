<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 27.10.15
 * Time: 14:59
 *
 * This class is a service named "menu" as defined in "app/config/service.yml"
 *
 * The service "menu" is also accessible in twig environment via the globals variables
 * as defined in "app/config/config.yml"
 *
 * # Twig Configuration
 *   twig:
 *      globals:
 *          menu: "@menu"
 *
 * So in twig: {{ dump(menu) }}
 *
 */

namespace AppBundle\Utils\Menu;

use Doctrine\Common\Annotations\Reader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route as RouteAnnotation;
use AppBundle\Utils\Menu\Menu as MenuAnnotation;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Router;



class MenuRenderer {

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var Router
     */
    protected $router;


    /**
     * @var string
     */
    protected $menuAnnotationClass = 'AppBundle\\Utils\\Menu\\Menu';

    /**
     * @var string
     */
    protected $routeAnnotationClass = 'Symfony\\Component\\Routing\\Annotation\\Route';

    /**
     * @var array
     */
    protected $controllersClass;

    /**
     * @var ArrayCollection
     */
    protected $container;

    public function __construct(Reader $reader,Kernel $kernel,Router $router){
        $this->reader = $reader;
        $this->kernel = $kernel;
        $this->router = $router;
        $this->container = new ArrayCollection();

        /* Load controllers class */
        $this->findControllersClass();

        /* Compile Menu by searching in controllers class */
        foreach($this->controllersClass as $controlerClass)
        {
            $this->loadController($controlerClass);
        }
        /* reoder menu item in collection */
        $this->reorderMenuItem();
    }

    /**
     * Return all the menu collection
     *
     * @return ArrayCollection
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Return a collection of item of a block
     *
     * @param $blockName
     * @return null|ArrayCollection
     * @throws \Exception
     */
    public function bloc($blockName)
    {
        if(!$this->container->containsKey($blockName))
        {
            throw new \Exception("Menu container didn't contain a block named: ".$blockName);
        }
        return $this->container->get($blockName);
    }

    /**
     *
     */
    public function allBlockToCategorySearch()
    {
        $returned    = array();

        /** @var ArrayCollection $block */
        foreach($this->container as $block)
        {
            /** @var MenuItem $item */
            foreach($block as $item)
            {
                $returned[] = array('title'=>$item->label,'url'=>$this->router->generate($item->routeName));
            }
        }

        return $returned;
}


    /**
     * This function return the class name of all the controllers
     * in the "src" directory.
     */
    protected function findControllersClass()
    {
        /*
         * todo: il y a certainement un meilleurs moyen de charger tout les controllers
         * todo: peut etre en chargant depuis le cache...a explorer!
         */
        $finder = new Finder();
        $srcDir = $this->kernel->getRootDir() . "/../src";
        $finder->files()->in($srcDir)->name("*Controller.php");

        $this->controllersClass = array();
        foreach ($finder as $file) {
            $fileName = $file->getRelativePathname();
            $className = str_replace('/','\\',$fileName);
            $this->controllersClass[] = str_replace('.php','',$className);
        }
    }

    /**
     * @param $controlerClass
     * @throws \InvalidArgumentException
     */
    protected function loadController($controlerClass)
    {
        if (!class_exists($controlerClass)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $controlerClass));
        }

        $class = new \ReflectionClass($controlerClass);
        if ($class->isAbstract()) {
            throw new \InvalidArgumentException(sprintf('Annotations from class "%s" cannot be read as it is abstract.', $controlerClass));
        }

        foreach ($class->getMethods() as $method) {

            /** @var MenuAnnotation $menuAnnotation */
            $menuAnnotation = null;

            /** @var RouteAnnotation $routeAnnotation */
            $routeAnnotation = null;

            foreach ($this->reader->getMethodAnnotations($method) as $annotation) {

                if ($annotation instanceof $this->menuAnnotationClass) {
                    $menuAnnotation = $annotation;
                }
                if ($annotation instanceof $this->routeAnnotationClass) {
                    $routeAnnotation = $annotation;
                }
            }

            if($menuAnnotation != null)
            {
                if($routeAnnotation != null)
                {
                    $this->addMenuItem(new MenuItem($menuAnnotation,$routeAnnotation));
                }
                else
                {
                    throw new \InvalidArgumentException(
                        sprintf('@Menu annotation should be accompanied with a @Route annotation in "%s", "%s" ',
                            $method->class, $method->getName()));
                }
            }
        }
    }

    /**
     * Internal function called by "loadController" method
     * @param MenuItem $item
     */
    protected function addMenuItem(MenuItem $item)
    {
        if($this->container->containsKey($item->block))
        {
            $this->container->get($item->block)->add($item);
        }
        else
        {
            //create new collection if it is the first item
            $this->container->set($item->block,new ArrayCollection());
            $this->container->get($item->block)->add($item);
        }
    }

    /**
     * This function reorder all the menu item in theirs block.
     *
     * The orderning is based on the "order" propertiy.
     *
     * If "order" is null, then the item is put in the end
     * of the collection.
     */
    protected function reorderMenuItem()
    {
        foreach($this->container as $blockName => $block)
        {
            $iterator = $block->getIterator();
            $iterator->uasort(function ($previous, $next) {
                if($previous->order == null) //if previous is null then increment index
                    return 1;
                if($next->order == null) //if next is null then decrement index
                    return -1;
                return ($previous->order < $next->order) ? -1 : 1;
            });
            $this->container[$blockName] = new ArrayCollection(iterator_to_array($iterator));
        }
    }





}