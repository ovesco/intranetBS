<?php

namespace AppBundle\Utils\ListRenderer;

class ActionMasse extends Action
{

    function __construct($label, $icon, $event = null)
    {
        $this->label = $label;
        $this->icon = $icon;
        $this->event = $event;
    }
}