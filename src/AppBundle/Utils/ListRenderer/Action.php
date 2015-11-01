<?php

namespace AppBundle\Utils\ListRenderer;

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
}
