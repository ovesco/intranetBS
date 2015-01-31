<?php

namespace Interne\SecurityBundle\Securer\Exceptions;

class UnknownVerificateurException extends \Exception {

    public function __construct($verificateur) {

        $message = "La classe '" . $verificateur . "' n'existe pas. Vérifiez que vous ayez bien créé un verificateur pour" .
        " la ressource correspondante dans Interne/SecurityBundle/Securer/Exceptions.";

        parent::__construct($message);
    }
}