<?php

/**
 * Le parser permet de convertir des données de manière simple
 */
namespace AppBundle\Utils\Accessor;

use Doctrine\ORM\EntityManager;


class Parser {


    private $em;

    public function __construct(EntityManager $em) {

        $this->em = $em;
    }




    /**
     * La méthode parseToString récupère en entrée une valeur inconnue et va tenter d'en déterminer le type, puis d'en
     * retourner une visualisation string.
     * Sachant que dans la validation, si l'on modifie une entité, on aura une chaine du type newValue = __entity__id__
     * on pourra ainsi retourner une string
     * @param $data mixed l'input
     * @return string
     */
    public function parseToString($data) {


        if($data instanceof \Datetime)
            return $data->format('dd.mm.YYYY');

        return $data;
    }

    /**
     * La méthode encode prend en paramètre n'importe quel type de valeur, et va le transformer en string pour pouvoir
     * le stocker
     * @param $data mixed input
     * @return string
     */
    public function encode($data) {

        switch(gettype($data)) {

            /*
             * ARRAY
             * =====
             * Retourne une version encodée en JSON de l'array
             */
            case 'array':
                return 'array__' . json_encode($data);
                break;

            /*
             * OBJET
             * =====
             * On détermine le type de l'objet, et en fonction de celui-ci on va tenter d'en récupérer les données
             * clé pour pouvoir en génerer une string sans avoir besoin de le serializer
             */
            case 'object':

                if($data instanceof \DateTime)
                    return 'datetime__' . $data->format('Y-m-d');

                else if(method_exists($data, 'getId'))
                    return get_class($data) . '__' . $data->getId();

                break;

            default:
                return $data;
                break;
        }
    }

    /**
     * Va décoder des données encodées par le Parser
     * @param $data string la donnée à décoder
     * @return mixed
     */
    public function decode($data) {

        $donnees = explode('__', $data);
        if(count($donnees) < 2) return $data;

        switch($donnees[0]) {

            case 'array':
                return json_decode($donnees[1]);
                break;

            case 'datetime':
                return new \DateTime($donnees[1]);
                break;
        }

        return $data;
    }
}