<?php

namespace AppBundle\Twig;

use ReflectionClass;

class GetClassFilter extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('get_class', array($this, 'getClassFilter')),
        );
    }

    public function getClassFilter($object)
    {
        $reflect = new ReflectionClass($object);
        return $reflect->getShortName();

    }

    public function getName()
    {
        return 'get_class_filter';
    }
}