<?php

namespace AppBundle\Utils\ListUtils;


class ActionList extends Action
{

    /**
     * @param $label
     * @param $icon
     * @param $route
     * @param null $routeParameters
     * @param null $postActions
     * @param null $condition
     * @param null $style ======> attribut class du boutton
     */
    function __construct($label, $icon, $route, $routeParameters = null, $postActions = null, $condition = null, $style = null)
    {
        parent::__construct($label, $icon, $route, $routeParameters, $postActions, $condition);

        $this->label = $label;
        $this->icon = $icon;
        $this->route = $route;
        $this->routeParameters = $routeParameters;
        $this->postActions = $postActions;
        $this->condition = $condition;
        $this->style = $style;
    }

}
