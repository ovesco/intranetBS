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
}