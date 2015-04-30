<?php

namespace AppBundle\Utils\Data;

/**
 * Class Sanitizer
 * Offre diverses methodes utiles pour traiter des données
 * La classe ne doit pas être enregistrées en tant que service, et toutes ses méthodes doivent être statiques
 * @package AppBundle\Utils\Data
 */
class Sanitizer {

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

    /**
     * Nettoie une string comme un nom. C'est-à-dire que tous les caractères spéciaux
     * sont supprimés, et les caractères d'après espaces sont mis en majuscule
     * @param $string
     * @return string
     */
    public static function cleanNames($string) {

        $string = strtolower(str_replace('-', ' ', $string));
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    }
}