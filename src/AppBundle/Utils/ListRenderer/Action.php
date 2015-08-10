<?php

namespace AppBundle\Utils\ListRenderer;

abstract class Action
{

    /** @var String */
    protected $label;

    /** @var String */
    protected $icon;

    /** @var String */
    protected $event;

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
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param String $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

}
