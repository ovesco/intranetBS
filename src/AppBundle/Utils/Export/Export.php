<?php

namespace AppBundle\Utils\Export;

/**
 * Class Export
 * Cette classe fournit diverses méthodes pour formatter les données afin de les utiliser dans des fichiers PDF ou
 * Excel à l'aide des services 'fpdf' ou 'excel'
 * @package AppBundle\Utils\Export
 */
class Export {


    /**
     * Permet de génerer un tableau de donnée sur un fichier PDF
     * @param FPDF $pdf instance de FPDF
     * @param array $header les noms des colonnes
     * @param array $data les données
     * @throws \Exception si les headers n'ont pas la même taille que les données
     *
     */
    public static function generatePHPData(FPDF $pdf, array $header, array $data) {

        if(count($header) != count($data))
            throw new \Exception("Headers et données pas de la même taille");
    }
}