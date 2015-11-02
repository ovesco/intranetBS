<?php

namespace AppBundle\Utils\ListRenderer;

class Action
{

    /** @var String */
    protected $label;

    /** @var String */
    protected $icon;

    /** @var String */
    protected $route;

    /** @var String */
    protected $routeParameters;

    /** @var String */
    protected $postActions;

    /** @var String */
    protected $condition;

    /** @var boolean */
    protected $inLine;

    /** @var boolean */
    protected $inMass;


    function __construct($label, $icon, $route, $routeParameters = null, $postActions = null, $condition = null, $inLine = true, $inMass = true)
    {
        $this->label = $label;
        $this->icon = $icon;
        $this->route = $route;
        $this->routeParameters = $routeParameters;
        $this->postActions = $postActions;
        $this->condition = $condition;
    }

    /**
     * @return String
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param String $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return String
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param String $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return String
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param String $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return String
     */
    public function getRouteParameters($item)
    {
        /* Petite subtilité pour pouvoir appler la fonction */
        $function = $this->routeParameters;
        return json_encode($function($item));
    }

    /**
     * @return String
     */
    public function getPostActions()
    {
        return $this->postActions;
    }

    /**
     * @return mixed
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @param mixed $condition
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;
    }


    /**
     * @return boolean
     */
    public function isInLine()
    {
        return $this->inLine;
    }

    /**
     * @param boolean $inLine
     */
    public function setInLine($inLine)
    {
        $this->inLine = $inLine;
    }

    /**
     * @return boolean
     */
    public function isInMass()
    {
        return $this->inMass;
    }

    /**
     * @param boolean $inMass
     */
    public function setInMass($inMass)
    {
        $this->inMass = $inMass;
    }

}
