<?php

namespace AppBundle\Twig;

class ExpediableFilter extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('expediable', array($this, 'expediableFilter')),
        );
    }

    public function booleanFilter($value)
    {
        if($value) return 'Courrier à cette adresse';
        else if(!$value) return 'Aucun courrier à cette adresse';
        else return '';
    }

    public function getName()
    {
        return 'expediable_filter';
    }
}