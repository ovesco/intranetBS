<?php

namespace AppBundle\Twig;

class VisualExtension extends \Twig_Extension {

    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return array(
            'datesToPercents' => new \Twig_Function_Method($this, 'datesToPercents')
        );
    }

    /**
     * Retourne un int représentatif de la position actuelle dans le temps compris entre deux dates
     * compris entre 1 et 100
     * @param \Datetime $debut
     * @param \Datetime $fin
     * @return int
     */
    public function datesToPercents ($debut, $fin) {

        $now = new \Datetime("now");

        if(!$fin instanceof \Datetime) // pas de fin
            return 50;

        else if($fin < $now) // attribution terminée
            return 100;

        /*
         * Attribution pas terminée
         * On convertit les trois dates en timestamp, et on fait une bête règle de trois
         */
        $debut  = $debut->getTimestamp();
        $now    = $now->getTimestamp();
        $fin    = $fin->getTimestamp();

        $dist   = $fin - $debut;
        $min    = $now - $debut;

        return ($min/$dist)*100;

    }

    public function getName()
    {
        return 'visual_extension';
    }
}