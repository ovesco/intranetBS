<?php

namespace Interne\SecurityBundle\Securer\Ressource\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class SecureRessource extends Annotation
{
    public $role;

    public $resource;

}