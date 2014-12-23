<?php

namespace AppBundle\Utils\Export;

use fpdf\FPDF;
use Symfony\Component\BrowserKit\Response;

/**
 * Class Pdf
 * Classe permettant de génerer un fichier PDF. étand FPDF pour fournir différentes méthodes bien pratiques
 * @package AppBundle\Utils\Export
 */
class Pdf extends FPDF {

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
     * Retourne un objet response formaté pour renvoyer un fichier PDF, avec le fichier PDF courant en contenu
     * @return Response
     */
    public function getResponse() {

        $response = new Response(

            $this->Output(),
            Response::HTTP_OK,
            array('content-type' => 'application/pdf')
        );

        $d = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'liste.pdf'
        );

        $response->headers->set('Content-Disposition', $d);
        return $response;
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