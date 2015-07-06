<?php

namespace Interne\FinancesBundle\Twig;

class MoneyFilter extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('money', array($this, 'moneyFilter')),
        );
    }

    public function moneyFilter($value)
    {
        $decimals = $value - floor($value);
        $format = number_format($value,2,'.','\'');
        if($decimals == 0){
            $result = explode('.',$format);
            return $result[0].'.-';
        }
        else{
            return $format;
        }

    }

    public function getName()
    {
        return 'money_filter';
    }
}