<?php

namespace AppBundle\Utils\ListRenderer;

class ActionLigne extends Action
{

    private $condition;

    function __construct($label, $icon, $route, $routeParameters = null, $postActions = null, $condition = null)
    {
        $this->label = $label;
        $this->icon = $icon;
        $this->route = $route;
        $this->routeParameters = $routeParameters;
        $this->condition = $condition;
        $this->postActions = $postActions;
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
