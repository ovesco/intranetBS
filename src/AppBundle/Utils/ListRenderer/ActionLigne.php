<?php

namespace AppBundle\Utils\ListRenderer;

class ActionLigne extends Action
{

    private $condition;

    function __construct($label, $icon, $event = null, $condition = null)
    {
        $this->label = $label;
        $this->icon = $icon;
        $this->event = $event;
        $this->condition = $condition;
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
