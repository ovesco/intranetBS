<?php

namespace Interne\SecurityBundle\Securer\Exceptions;

/**
 * Class RessourceLockedException
 * Exception balancée lorsque la ressource souhaitée n'est pas accessible
 * @package Interne\SecurityBundle\Securer\Exceptions
 */
class RessourceLockedException extends \Exception {

    public $ressource;

    public $action;

}