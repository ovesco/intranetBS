<?php

namespace AppBundle\Utils\ListUtils;

class ActionLine extends Action
{
    /** @var boolean */
    protected $inLine;

    /** @var boolean */
    protected $inMass;


    /**
     * @param $label
     * @param $icon
     * @param $route
     * @param null $routeParameters
     * @param null $postActions
     * @param null $condition
     * @param bool $inLine
     * @param bool $inMass
     */
    function __construct($label, $icon, $route, $routeParameters = null, $postActions = null, $condition = null, $inLine = true, $inMass = true)
    {
        parent::__construct($label, $icon, $route, $routeParameters, $postActions, $condition);

        $this->inLine = $inLine;
        $this->inMass = $inMass;
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
