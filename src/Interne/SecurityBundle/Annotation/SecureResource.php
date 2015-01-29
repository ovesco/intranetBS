<?php

namespace Interne\SecurityBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class SecureResource extends Annotation
{
    public $role;

    public $resource;
}