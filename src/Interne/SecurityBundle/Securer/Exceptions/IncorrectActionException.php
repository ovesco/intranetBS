<?php

namespace Interne\SecurityBundle\Securer\Exceptions;

class IncorrectActionException extends \Exception {

    public function __construct($action, $params) {

        $message = "L'action '" . $action . "' n'est pas disponible. Actions possibles : ";
        foreach($params as $p)
            $message .= "'" .$p. "' ";

        parent::__construct($message);
    }
}