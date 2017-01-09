<?php

namespace AppBundle\Annotations\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 * @Target({"METHOD"})
 */
class Excel {

    public $reader;
}