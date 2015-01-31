<?php

namespace Interne\SecurityBundle\Securer\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class SecureRessource extends Annotation
{
    public $action;

    public $ressource;
}