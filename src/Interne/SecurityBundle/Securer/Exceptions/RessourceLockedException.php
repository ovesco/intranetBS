<?php

namespace Interne\SecurityBundle\Securer\Exceptions;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class RessourceLockedException
 * Exception balancée lorsque la ressource souhaitée n'est pas accessible
 * @package Interne\SecurityBundle\Securer\Exceptions
 */
class RessourceLockedException extends AccessDeniedException {

    public $ressource;

    public $action;

    public function __construct($message) {

        parent::__construct($message);
    }

}