<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Personne;
use ReflectionClass;

/**
 * Cette class est un container qui contient tout les filtres et autre ajout
 * que l'on souhaite faire dans twig.
 *
 *
 * Class AppExtension
 * @package AppBundle\Twig
 */
class AppExtension extends \Twig_Extension
{
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'app_extension';
    }

    /** @var \Twig_Environment null  */
    private $environment = null;

    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getFunctions()
    {
        return array(
            'apply_filter' => new \Twig_Function_Method($this, 'applyFilter')
        );
    }

    /**
     * Returns a list of global variables to add to the existing list.
     *
     * @return array An array of global variables
     */
    function getGlobals(){
        return array(
            'global_date_format'=> 'd.m.Y',
        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('boolean', array($this, 'boolean_filter')),
            new \Twig_SimpleFilter('genre', array($this, 'genre_filter')),
            new \Twig_SimpleFilter('get_class', array($this, 'get_class_filter')),
        );
    }

    public function boolean_filter($boolean)
    {
        if($boolean) return 'Oui';
        else if(!$boolean) return 'Non';
        else return '';
    }

    public function get_class_filter($object)
    {
        $reflect = new ReflectionClass($object);
        return $reflect->getShortName();

    }

    public function genre_filter($value)
    {
        return ($value == 'm') ? Personne::HOMME : Personne::FEMME;
    }

    /**
     * Tentative de fonciton pour appeler de facon générique des filtres.
     *
     * @param $context
     * @param $filterName
     * @return null
     */
    public function applyFilter($context, $filterName)
    {

        $filter = $this->environment->getFilter($filterName);

        $callable = $filter->getCallable();


        $string = __NAMESPACE__ ."\\".get_class($filter).'::'.$callable;
        //var_dump(call_user_func($callable));

        //var_dump(call_user_func_array($callable,array($this->environment,$context)));
        return null;

        // handle parameters here, by calling the
        // appropriate filter and pass $context there
    }

}



