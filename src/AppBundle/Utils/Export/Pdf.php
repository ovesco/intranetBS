<?php

namespace AppBundle\Utils\Export;

use AppBundle\Entity\Adresse;
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
     * Ajout d'un document PDF à la suite.
     * @param Pdf $documentToAdd
     */
    public function AddPageWithPdf(Pdf $documentToAdd)
    {
        //Attribue un nom de fichier aleatoire et temporaire
        $fileName = 'temporary/pdf_tmp_'.str_shuffle('1234567890').'pdf';
        //on sauve le fichier dans le dossier temporaire
        $documentToAdd->Output($fileName,'F');

        //fusion des deux PDF
        $pageCount = $this->setSourceFile($fileName);

        for($i=0; $i<$pageCount; $i++){
            $this->AddPage();
            $tplIdx = $this->importPage($i+1, '/MediaBox');
            $this->useTemplate($tplIdx);
        }

        //Supression du fichier temporaire
        unlink($fileName);

    }

    /*
     * L'adresse du membre ou de la famille
     * sera ajoutée au PDF dans l'espace prévu
     * pour les evelloppes à fenêtres.
     *
     * Note: il faut donner un tableau $adressePrincipale en parametre qui est
     * le résultat des fonctions ->getAdressePrinipale() des Membres et Familles.
     */
    /**
     * @param $adressePrincipale
     */
    public function addAdresseEnvoi($adressePrincipale)
    {
        $x =  110;
        $y =  50;
        $h = 4;
        $this->SetXY($x,$y);
        $this->SetFont('Arial','',9);

        $origine = $adressePrincipale['origine'];
        $adresse = $adressePrincipale['adresse'];
        $owner = $adressePrincipale['owner'];


        if($owner['class'] == 'Membre')
        {
            $this->Cell(50,$h,ucfirst($owner['nom']).' '.ucfirst($owner['prenom']));
        }
        elseif($owner['class'] == 'Famille')
        {
            $this->Cell(50,$h,'Famille '.ucfirst($owner['nom']));
        }
        else
        {
            //erreur
        }

        if($adresse->getMethodeEnvoi() == 'Courrier')
        {
            $y = $y+$h;
            $this->SetXY($x,$y);
            $this->Cell(50,$h,ucfirst($adresse->getRue()));
            $y = $y+$h;
            $this->SetXY($x,$y);
            $this->Cell(50,$h,$adresse->getNpa().' '.ucfirst($adresse->getLocalite()));

        }
        elseif($adresse->getMethodeEnvoi() == 'Email')
        {
            $y = $y+$h;
            $this->SetXY($x,$y);
            $this->Cell(50,$h,$adresse->getEmail());
        }
        else
        {
            $this->MultiCell(50,30,'Adresse non définie');
        }
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