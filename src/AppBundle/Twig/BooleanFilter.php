<?php

namespace AppBundle\Twig;

class BooleanFilter extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('boolean', array($this, 'booleanFilter')),
        );
    }

    public function booleanFilter($value)
    {
        if($value) return 'Oui';
        else if(!$value) return 'Non';
        else return '';
    }

    public function getName()
    {
        return 'boolean_filter';
    }
}