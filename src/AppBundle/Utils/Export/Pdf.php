<?php

namespace AppBundle\Utils\Export;

use AppBundle\Entity\Adresse;
use AppBundle\Entity\Membre;
use AppBundle\Entity\Famille;
use AppBundle\Entity\Mere;
use AppBundle\Entity\Pere;
use FPDF;
use FPDI;
use Symfony\Component\BrowserKit\Response;

/**
 * Class Pdf
 * Classe permettant de génerer un fichier PDF. étand FPDF pour fournir différentes méthodes bien pratiques
 * @package AppBundle\Utils\Export
 */
class Pdf extends FPDI {

    private $kernel;

    public function __construct($krenel)
    {
        parent::__construct();
        $this->kernel = $krenel;
    }

    /**
     * Génère un entête de base sur le fichier PDF
     */
    public function defaultHeader() {

        //todo à corriger
        //$this->Image('bundles/app/images/main_logo.png',10,6,17,17);
        $this->SetFont('Arial','',12);

        $this->Cell(20);
        $this->Cell(30,10,'Brigade de Sauvabelin');
        $this->Ln(20);
    }

    /**
     * Permet de charger un fichier PDF comme template
     * @param $src
     */
    public function loadTemplate($src) {

        $pageCount = $this->setSourceFile($src);

        $tplIdx = $this->importPage(1, '/MediaBox');
        $this->addPage();
        $this->useTemplate($tplIdx);
    }

    public function loadTemplateWithMuliPage($src) {

        $pageCount = $this->setSourceFile($src);

        for ($i = 1; $i <= $pageCount; $i++) {
            $tplIdx = $this->importPage($i);
            $this->AddPage();
            $this->useTemplate($tplIdx);
        }
    }



    public function init($top = 30) {

        $this->addPage();
        $this->setY($top);
    }

    /**
     * Ajout d'un document PDF à la suite.
     * @param Pdf $documentToAdd
     */
    public function AddPageWithPdf(Pdf $documentToAdd)
    {



        $fileName = $this->getTemporaryFileName();
        //on sauve le fichier dans le dossier temporaire
        $documentToAdd->Output($fileName,'F');

        //fusion des deux PDF
        $pageCount = $this->setSourceFile($fileName);

        for($i=1; $i<=$pageCount; $i++){
            $this->AddPage();
            $tplIdx = $this->importPage($i);
            $this->useTemplate($tplIdx);
        }

        //Supression du fichier temporaire
        unlink($fileName);
    }

    /**
     * @param $arrayOfPath
     */
    public function fusionOfDocuments($arrayOfPath)
    {
        foreach($arrayOfPath as $path)
        {
            $this->loadTemplateWithMuliPage($path);
            unlink($path);
        }
    }

    /**
     * @param $pageNumber
     * @param Pdf $pagePdf
     * @return Pdf
     */
    public function replacePage($pageNumber,Pdf $pagePdf)
    {
        $clone = clone $this;

        $fileNameThis = $clone->saveInTemporaryFolder();
        $fileNamePage = $pagePdf->saveInTemporaryFolder();

        //on sauve les fichiers dans le dossier temporaire
        $pagePdf->Output($fileNamePage,'F');
        $clone->Output($fileNameThis,'F');


        $resultPdf = new self($this->kernel);


        $pageCount = $resultPdf->setSourceFile($fileNameThis);



        for ($i = 1; $i <= $pageCount; $i++) {


            if($pageNumber == $i){
                //on modifie le fichier source
                $resultPdf->setSourceFile($fileNamePage);
                $tplIdx = $resultPdf->importPage(1);
                $resultPdf->AddPage();
                $resultPdf->useTemplate($tplIdx);

                // on remet le bon fichier source
                $resultPdf->setSourceFile($fileNameThis);
            }
            else{
                $tplIdx = $resultPdf->importPage($i);
                $resultPdf->AddPage();
                $resultPdf->useTemplate($tplIdx);
            }


        }



        //Supression du fichier temporaire
        unlink($fileNameThis);
        unlink($fileNamePage);

        return $resultPdf;

    }

    /**
     * @param $pageNumber
     * @return Pdf
     */
    public function getPage($pageNumber)
    {

        //nouvelle instance
        $pageToReturn = new self($this->kernel);

        //clone du document actuelle
        $clone = clone $this;

        $fileName = $this->getTemporaryFileName();



        //on sauve le fichier dans le dossier temporaire
        $clone->Output($fileName,'F');




        $pageToReturn->setSourceFile($fileName);



        $pageToReturn->AddPage();
        $tplIdx = $pageToReturn->importPage($pageNumber);
        $pageToReturn->useTemplate($tplIdx);

        //Supression du fichier temporaire
        unlink($fileName);

        return $pageToReturn;
    }

    /**
     * @return string
     */
    public function saveInTemporaryFolder()
    {
        $fileName = $this->getTemporaryFileName();
        $this->Output($fileName,'F');
        return $fileName;
    }

    /**
     * @param $fileName
     * @param $kernel
     * @return Pdf
     */
    public static function getInTemporaryFolder($fileName,$kernel)
    {
        $document = new self($kernel);
        $document->loadTemplateWithMuliPage($fileName);
        //unlink($fileName);
        return $document;
    }


    private function getTemporaryFileName()
    {
        //Attribue un nom de fichier aleatoire et temporaire
        $path = $this->kernel->getRootDir() . '/cache/' . $this->kernel->getEnvironment().'/temporary_pdf';
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        return $path.'/pdf_tmp_'.str_shuffle('1234567890abcdefghijk').'.pdf';
    }




    /**
     * L'adresse du membre ou de la famille
     * sera ajoutée au PDF dans l'espace prévu
     * pour les evelloppes à fenêtres.
     *
     * Note: il faut donner un tableau $adresseExpedition en parametre qui est
     * le résultat des fonctions ->getAdresseExpedition() des Membres et Familles.
     *
     *
     * @param $adresseExpedition
     */
    public function addAdresseEnvoi($adresseExpedition)
    {

        $x =  110;
        $y =  50;
        $h = 4;
        $this->SetXY($x,$y);
        $this->SetFont('Arial','',9);

        if($adresseExpedition != null){
            $adresse = $adresseExpedition['adresse'];
            $owner = $adresseExpedition['ownerEntity'];

            switch($owner->className())
            {
                case Membre::className():
                case Pere::className():
                case Mere::className():
                    $this->Cell(50,$h,ucfirst($owner->getNom()).' '.ucfirst($owner->getPrenom()));
                    break;
                case Famille::className():
                    $this->Cell(50,$h,'Famille '.ucfirst($owner->getNom()));
                    break;
            }


            $y = $y+$h;
            $this->SetXY($x,$y);
            $this->Cell(50,$h,ucfirst($adresse->getRue()));
            $y = $y+$h;
            $this->SetXY($x,$y);
            $this->Cell(50,$h,$adresse->getNpa().' '.ucfirst($adresse->getLocalite()));
        }
        else{
            $this->Cell(50,$h,'Aucune adresse trouvée');
        }
    }

    public function tagInTopRight($string)
    {
        $x =  170;
        $y =  10;
        $h = 4;
        $this->SetXY($x,$y);
        $this->SetFont('Arial','',8);
        $this->Cell(30,$h,$string);
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
            $this->setY($top);

        $totalIndices   = 0;

        foreach($colWidth as $i)
            $totalIndices += $i;

        $ratio          = 185/$totalIndices;

        for($i = 0; $i < count($colWidth); $i++)
            $colWidth[$i] = $colWidth[$i]*$ratio;

        /*
         * On imprimme les headers
         * Ligne stylisée avec une police spéciale
         */
        $this->SetFont('arial', 'B', 11);
        for($i = 0; $i < count($headers); $i++)
            $this->Cell($colWidth[$i],7,$headers[$i],'B',0,'L');

        $this->ln();

        /*
         * impression des données
         * Police de base, on balance en masse
         */
        $this->SetFont('arial', '', 11);
        for($i = 0; $i < count($data); $i++) {

            foreach ($data[$i] as $val)
                $this->Cell($colWidth[$i], 7, $val, 0, 0, 'L');

            $this->ln();
        }

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