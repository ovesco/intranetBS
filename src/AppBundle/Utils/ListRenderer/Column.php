<?php

namespace AppBundle\Utils\ListRenderer;

class Column
{
    /** @var String */
    private $itemRenderer;

    private $twigFilters;

    /** @var String */
    private $name;

    public function __construct($name, $itemRenderer, $twig_filters = null, $pattern = null)
    {
        $this->name = $name;
        $this->itemRenderer = $itemRenderer;
        $this->twigFilters = $twig_filters;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getTwigFilters()
    {
        return $this->twigFilters;
    }

    public function render($item)
    {
        /* Petite subtilité pour pouvoir appler la fonction */
        $function = $this->itemRenderer;
        return $function($item);
    }
}