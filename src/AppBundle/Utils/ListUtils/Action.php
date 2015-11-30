<?php

namespace AppBundle\Utils\ListUtils;

abstract class Action
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

    /** @var mixed c'est une fonction */
    protected $condition;


    function __construct($label, $icon, $route, $routeParameters = null, $postActions = null, $condition = null)
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
     * @param $item
     * @return String
     */
    public function getRouteParameters($item = null)
    {
        /* Petite subtilité pour pouvoir appeler la fonction */
        $function = $this->routeParameters;

        if ($item == null)
            return json_encode($function());
        else
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

}
