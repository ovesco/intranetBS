<?php


namespace AppBundle\Utils\ListRender;

class Column {

    /**
     * cette variable contient une fonction
     * @var
     */
    private $dataAccessor;
    private $twigFilters;
    private $name;

    public function __construct($name,$dataAccessor,$twig_filters = null,$pattern = null){
        $this->name = $name;
        $this->dataAccessor = $dataAccessor;
        $this->twigFilters = $twig_filters;
    }

    /**
     * @param string $name
     */
    public function setName($name){
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }


    public function getTwigFilters()
    {
        return $this->twigFilters;
    }

    public function render($object)
    {
        /*
         * Petite subtilité pour pouvoir appler la fonction.
         */
        $function = $this->dataAccessor;
        return $function($object);
    }
}