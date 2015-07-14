<?php

namespace Interne\FinancesBundle\Utils;

use AppBundle\Utils\Parametre\Parametres;
use Interne\FinancesBundle\Entity\Facture;
use AppBundle\Utils\Export\Pdf;
use Doctrine\ORM\EntityManager;



class FacturePrinter
{

    private $em;
    private  $parametres;
    private $pdf;

    public function __construct(EntityManager $em, Parametres $parametres, Pdf $pdf){

        $this->em = $em;
        $this->parametres = $parametres;
        $this->pdf = $pdf;
    }

    /**
     * Cette facture appose le contenu d'une facture sur le document PDF
     * qui lui est passé en argument.
     *
     * @param Facture $facture
     * @return Pdf
     */
    public function factureToPdf(Facture $facture)
    {
        /*
         * On récupère les parametres nécaissaires
         * a la création de la facture en PDF
         */


        $ccpBvr = $this->parametres->getValue('finance','impression_ccp_bvr');
        $adresse = $this->parametres->getValue('info_groupe','adresse');
        $modePayement = $this->parametres->getValue('finance','impression_mode_payement');
        $texteFacture = $this->parametres->getValue('finance','impression_texte_facture');
        $affichageMontant = $this->parametres->getValue('finance','impression_affichage_montant');

        /*
         * Infos utile de la facture
         */
        $numeroReference = (string)$facture->getId();
        $montant = (string)$facture->getMontantEmis();

        $title = 'Facture N°'.$facture->getId();

        $this->pdf->AddPage();
        $this->pdf->SetAutoPageBreak(false);
        $this->pdf->SetLeftMargin(20);
        $this->pdf->SetRightMargin(20);

        $this->pdf->SetFont('Arial','',9);

        $cellWidth = 50;//ne sert pas vraiment
        $cellHigh = 4;

        /*
         * Adresse haut de page
         */
        $x =  20;
        $y =  20;
        $this->pdf->SetXY($x,$y);
        $this->pdf->MultiCell($cellWidth,$cellHigh,$adresse);

        /*
         * Date
         */
        $x = 130;
        $y =  20;
        $this->pdf->SetXY($x,$y);
        $this->pdf->Cell($cellWidth,$cellHigh,'Lausanne, le ');



        /*
         * Titre de la facture
         */
        $this->pdf->SetFont('Arial','B',9);

        $x = 20;
        $y =  70;
        $this->pdf->SetXY($x,$y);
        $this->pdf->Cell(140,$cellHigh,$title);

        //retour à la ligne
        $this->pdf->ln();
        $this->pdf->ln();

        /*
        * Texte d'intro
        */
        $this->pdf->SetFont('Arial','',9);
        $this->pdf->write($cellHigh,$texteFacture);


        //retour à la ligne
        $this->pdf->ln();
        $this->pdf->ln();


        /*
         * Tableau facture
         */
        $this->pdf->SetFont('Arial','B',9);
        $this->pdf->Cell(110,$cellHigh,'');
        $this->pdf->Cell(30,$cellHigh,'Date');
        $this->pdf->Cell(20,$cellHigh,'Montant');
        $this->pdf->ln();
        $this->pdf->SetFont('Arial','',9);


        foreach($facture->getCreances() as $creance)
        {
            $proprietaire = null;
            if($creance->getOwner()->isClass('Membre'))
            {
                $proprietaire = $creance->getOwner()->getPrenom().' '.$creance->getOwner()->getNom();
            }
            else
            {
                $proprietaire = 'Famille '.$creance->getOwner()->getNom();
            }

            $titre = $proprietaire.' - '.$creance->getTitre();

            $this->pdf->SetFont('Arial','B',9);
            $this->pdf->Cell(110,$cellHigh,$titre,'T');
            $this->pdf->SetFont('Arial','',9);
            $this->pdf->Cell(30,$cellHigh,'date','T');
            $this->pdf->Cell(20,$cellHigh,number_format($creance->getMontantEmis(),2),'T');

            $this->pdf->ln();

            $remarque = $creance->getRemarque();
            if($remarque != null)
            {
                $this->pdf->Cell(20,$cellHigh,'Remarque:');
                $this->pdf->MultiCell(90,$cellHigh,$remarque);
            }
        }

        $i=1;
        foreach($facture->getRappels() as $rappel)
        {
            $this->pdf->Cell(110,$cellHigh,'Rappel N°'.$i,'T');
            $this->pdf->Cell(30,$cellHigh,'date','T');

            $this->pdf->Cell(20,$cellHigh,number_format($rappel->getMontantEmis(),2),'T');
            $this->pdf->ln();
            $i++;
        }

        $this->pdf->Cell(110,$cellHigh,'','T');
        $this->pdf->Cell(30,$cellHigh,'Tolal:','T');
        $this->pdf->Cell(20,$cellHigh,number_format($facture->getMontantEmis(),2).' CHF',1);


        if($modePayement == 'BVR')
        {
            $this->pdf = $this->insertBvr($adresse,$ccpBvr,$numeroReference,$affichageMontant,$montant);
        }
        elseif($modePayement == 'BV')
        {

        }
        else
        {

        }

        return $this->pdf;

    }



    /*
     * Crée les ligne de Codage BVR
     */
    /**
     * @param $numeroReference
     * @param string $type
     * @param string $ccp
     * @return mixed|string
     */
    private  function creatLineCode($numeroReference,$type = 'numRef',$ccp = '')
    {
        $numeroReferance = (string)$numeroReference;

        $codeLine = '';
        switch($type)
        {
            case 'numRef':
                $codeLine = '00 00000 00000 00000 00000 00000';
                $codeLineLenght = strlen($codeLine);
                $numeroReferanceLenght = strlen($numeroReferance);
                $spaceIndex = 0;
                for($i = 1; $i <= $numeroReferanceLenght; $i++)
                {
                    $num = substr($numeroReferance,$numeroReferanceLenght-$i,1);
                    $codeChar = substr($codeLine,$codeLineLenght-$spaceIndex-$i-1,1);
                    if($codeChar != '0')
                    {
                        $spaceIndex++;
                    }
                    $codeLine = substr_replace($codeLine,$num,$codeLineLenght-$spaceIndex-$i-1,1);
                }
                break;

            case 'code':
                $codeLine = '042>000000000000000000000000000+ 00000000>';
                $inputCcp = str_replace ('-','',$ccp);
                $codeLineLenght = strlen($codeLine);
                $inputCcpLenght = strlen($inputCcp);
                $codeLine = substr_replace($codeLine,$inputCcp,$codeLineLenght-$inputCcpLenght-1,$inputCcpLenght);
                $numeroReferanceLenght = strlen($numeroReferance);
                $codeLine = substr_replace($codeLine,$numeroReferance,$codeLineLenght-$numeroReferanceLenght-12,$numeroReferanceLenght);
                break;
        }
        return $codeLine;

    }


    /**
     * Ajouter un BVR
     *
     * @param $adresse
     * @param $ccp
     * @param $numeroReference
     * @param $affichageMontant
     * @param $montant
     * @return mixed
     */
    private function insertBvr($adresse,$ccp,$numeroReference,$affichageMontant,$montant)
    {

        /*
         * BVR Start Point (X = 0mm ,Y = 190mm) depuis le haut gauche de la page
         *
         *   o ------->X
         *   |
         *   |
         *   |
         *   v
         *   Y
         */

        $xStart = 0;
        $yStart = 190;
        /*
         * ligne de controle
         */
        $this->pdf->Line($xStart,$yStart,$xStart+5,$yStart);
        $this->pdf->Line($xStart+60,$yStart,$xStart+60,$yStart+5);
        $this->pdf->Line($xStart+205,$yStart,$xStart+210,$yStart);
        $this->pdf->Line($xStart+118,$yStart+80,$xStart+124,$yStart+80);
        $this->pdf->Line($xStart+121,$yStart+75,$xStart+121,$yStart+80);



        $cellWidth = 50;//ne sert pas vraiment
        $cellHigh = 4;

        $this->pdf->SetFont('Arial', '', 9);


        /*
         * Adresse récépissé
         */
        $x = $xStart + 5;
        $y = $yStart + 10;
        $this->pdf->SetXY($x,$y);
        $this->pdf->MultiCell($cellWidth,$cellHigh,$adresse);

        /*
         * compte récépissé
         */
        $x = $xStart + 28;
        $y = $yStart+42;
        $this->pdf->SetXY($x,$y);
        $this->pdf->Cell($cellWidth,$cellHigh,$ccp);

        /*
         * num. référence récépissé
         */
        $codeLine = $this->creatLineCode($numeroReference,'numRef');
        $x = $xStart + 5;
        $y = $yStart+60;
        $this->pdf->SetXY($x,$y);
        $this->pdf->Cell($cellWidth,$cellHigh,$codeLine);

        /*
         * Adresse virement
         */
        $x = $xStart + 65;
        $y = $yStart + 10;
        $this->pdf->SetXY($x,$y);
        $this->pdf->MultiCell($cellWidth,$cellHigh,$adresse);

        /*
         * compte virement
         */
        $x = $xStart + 89;
        $y = $yStart+42;
        $this->pdf->SetXY($x,$y);
        $this->pdf->Cell($cellWidth,$cellHigh,$ccp);

        /*
         * num. référance virement
         */
        $codeLine = $this->creatLineCode($numeroReference,'numRef');
        $x = $xStart + 130;
        $y = $yStart+38;
        $this->pdf->SetXY($x,$y);
        $this->pdf->Cell($cellWidth,$cellHigh,$codeLine);

        /*
         * code BVR en bas de coupon
         */
        $this->pdf->SetFont('Arial', '', 11);

        $codeLine = $this->creatLineCode($numeroReference,'code',$ccp);
        $x = $xStart+68;
        $y = $yStart+85;
        $this->pdf->SetXY($x,$y);
        $this->pdf->Cell($cellWidth,$cellHigh,$codeLine);

        if($affichageMontant == 'Oui')
        {
            /*
             * Montant sur le BVR
             */
            $this->pdf->SetFont('Arial', '', 9);

            $x = $xStart+50;
            $y = $yStart+50;
            $this->pdf->SetXY($x,$y);
            $this->pdf->Cell($cellWidth,$cellHigh,number_format($montant,2));

            $x = $xStart+90;
            $y = $yStart+50;
            $this->pdf->SetXY($x,$y);
            $this->pdf->Cell($cellWidth,$cellHigh,number_format($montant,2));
        }

        return $this->pdf;

    }

}

