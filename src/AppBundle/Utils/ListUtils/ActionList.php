<?php

namespace AppBundle\Utils\ListUtils;


class ActionList extends Action
{

    function __construct($label, $icon, $route, $routeParameters = null, $postActions = null, $condition = null)
    {
        parent::__construct($label, $icon, $route, $routeParameters, $postActions, $condition);

        $this->label = $label;
        $this->icon = $icon;
        $this->route = $route;
        $this->routeParameters = $routeParameters;
        $this->postActions = $postActions;
        $this->condition = $condition;
    }

}
