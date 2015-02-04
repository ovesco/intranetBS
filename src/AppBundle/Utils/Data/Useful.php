<?php

namespace AppBundle\Utils\Data;

/**
 * Class Useful
 * Offre diverses methodes utiles pour traiter des données
 * La classe ne doit pas être enregistrées en tant que service, et toutes ses méthodes doivent être statiques
 * @package AppBundle\Utils\Data
 */
class Useful {

    /**
     * Nettoie une chaine de charactères de tout caractère spécial
     * @param string $string
     * @return string
     */
    public static function cleanString($string) {

        $string = strtolower(str_replace(' ', '-', $string));
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        return preg_replace('/-+/', '-', $string);
    }
}