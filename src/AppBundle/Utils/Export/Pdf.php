<?php

namespace AppBundle\Utils\Export;

use fpdf\FPDF;
use fpdi\FPDI;
use Symfony\Component\BrowserKit\Response;

/**
 * Class Pdf
 * Classe permettant de génerer un fichier PDF. étand FPDF pour fournir différentes méthodes bien pratiques
 * @package AppBundle\Utils\Export
 */
class Pdf extends FPDI {

    /**
     * Génère un entête de base sur le fichier PDF
     */
    public function defaultHeader() {

        $this->Image('bundles/app/images/main_logo.png',10,6,17,17);
        $this->SetFont('Arial','',12);

        $this->Cell(20);
        $this->Cell(30,10,'Brigade de Sauvabelin');
        $this->Ln(20);
    }

    /**
     * Permet de charger un fichier PDF comme template
     * @param $src
     * @param $height
     */
    public function loadTemplate($src, $height) {

        $pageCount = $this->setSourceFile($src);

        $tplIdx = $this->importPage(1, '/MediaBox');
        $this->addPage();
        $this->useTemplate($tplIdx);

        $this->setX($height);
    }


    /**
     * La méthode printData permet d'imprimmer un tableau sur le PDF. La méthode va chercher dans le tableau une entrée
     * "headers" qui indiquera les noms des entêtes des colonnes. Ensuite, la méthode va simplement itérer sur les données
     * contenues et les imprimmer à la suite
     * @param array $headers les headers
     * @param array colWidth contient des indices de largeur compris entre 1 et 10. La méthode va essayer d'équilibrer
     *                       au mieux les largeurs des colonnes suivant ces indices
     * @param array $data les données
     * @param int $top à partir d'ou on commence à écrire sur le PDF
     */
    public function printData(array $headers, array $colWidth, array $data, $top = null) {

        if(!is_null($top))
            $this->setX($top);

        $totalIndices   = 0;

        foreach($colWidth as $i)
            $totalIndices += $i;

        $ratio          = $this->w/$totalIndices;

        for($i = 0; $i < count($colWidth); $i++)
            $colWidth[$i] = $colWidth[$i]*$ratio;

        /*
         * On imprimme les headers
         * Ligne stylisée avec une police spéciale
         */
        $this->SetFont('arial', 'B', 11);
        for($i = 0; $i < count($headers); $i++)
            $this->Cell($colWidth[$i],7,$headers[$i],'B',0,'C');

        $this->ln();

        /*
         * impression des données
         * Police de base, on balance en masse
         */
        $this->SetFont('arial', '', 11);
        for($i = 0; $i < count($data); $i++)
            foreach($data[$i] as $val)
                $this->Cell($colWidth[$i],7,$val,0,0,'L');

        $this->ln();

    }


    /*
     * Surcharge de la fonction pour prendre en charge
     * les accents du français avec 'utf8_decode'
     */
    function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='') {
        parent::Cell($w,$h, utf8_decode($txt), $border,$ln,$align,$fill,$link);
    }

    function MultiCell($w,$h=0,$txt='',$border=0,$ln=1,$align='',$fill=0,$link='') {
        parent::MultiCell($w,$h, utf8_decode($txt), $border,$ln,$align,$fill,$link);
    }
}