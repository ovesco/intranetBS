<?php

namespace Interne\FactureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Interne\FactureBundle\Entity\Rappel;
use Interne\FactureBundle\Entity\Facture;
use Interne\GlobalBundle\Services\Pdf;
use Interne\FactureBundle\Entity\Parametre;

class PrintController extends Controller
{
    public function printAjaxAction()
    {
        $request = $this->getRequest();

        if($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');

            $em = $this->getDoctrine()->getManager();
            $facture = $em->getRepository('InterneFactureBundle:Facture')->find($id);

            /*
             * Creation du PDF
             */
            $pdf = new Pdf();

            $pdf = $this->factureToPdf($facture,$pdf);


            $fileName = 'Facture_' . $facture->getId() . '.pdf';


            return new Response($pdf->Output($fileName,'D'));
        }

        return new Response();
    }


    public function printAction($id)
    {
            $em = $this->getDoctrine()->getManager();
            $facture = $em->getRepository('InterneFactureBundle:Facture')->find($id);


            /*
             * Creation du PDF
             */
            $pdf = new Pdf();
            $pdf = $this->factureToPdf($facture,$pdf);


            $fileName = 'Facture_' . $facture->getId() . '.pdf';

            $pdf->Output($fileName,'D');

        return new Response();
    }


    /*
     * Création du PDF associé à une facture.
     */
    /**
     * @param Facture $facture
     * @param Pdf $pdf
     * @return Pdf
     */
    private function factureToPdf(Facture $facture, Pdf $pdf)
    {
        /*
         * On récupère les parametres nécaissaires
         * a la création de la facture en PDF
         */
        $em = $this->getDoctrine()->getManager();
        $paramRepo = $em->getRepository('InterneGlobalBundle:Parametre');

        $ccpBvr = $paramRepo->findOneBy(array('name'=>'impression_ccp_bvr'))->getValue();
        $adresse = $paramRepo->findOneBy(array('name'=>'impression_adresse'))->getValue();
        $modePayement = $paramRepo->findOneBy(array('name'=>'impression_mode_payement'))->getValue();
        $texteFacture = $paramRepo->findOneBy(array('name'=>'impression_texte_facture'))->getValue();
        $affichageMontant = $paramRepo->findOneBy(array('name'=>'impression_affichage_montant'))->getValue();

        /*
         * Infos utile de la facture
         */
        $numeroReference = (string)$facture->getId();
        $montant = (string)$facture->getMontantTotal();
        $adresseFacturation = $facture->getOwnerAdresse();
        $title = 'Facture N°'.$facture->getId();




        $pdf->AddPage();
        $pdf->SetAutoPageBreak(false);
        $pdf->SetLeftMargin(20);
        $pdf->SetRightMargin(20);

        $pdf->SetFont('Arial','',9);

        $cellWidth = 50;//ne sert pas vraiment
        $cellHigh = 4;

        /*
         * Adresse haut de page
         */
        $x =  20;
        $y =  20;
        $pdf->SetXY($x,$y);
        $pdf->MultiCell($cellWidth,$cellHigh,$adresse);

        /*
         * Date
         */
        $x = 130;
        $y =  20;
        $pdf->SetXY($x,$y);
        $pdf->Cell($cellWidth,$cellHigh,'Lausanne, le ');

        /*
         * Adresse du membre
         */
        $x =  110;
        $y =  50;
        $pdf->SetXY($x,$y);
        $pdf->MultiCell($cellWidth,$cellHigh,$adresseFacturation);

        /*
         * Titre de la facture
         */
        $pdf->SetFont('Arial','B',9);

        $x = 20;
        $y =  70;
        $pdf->SetXY($x,$y);
        $pdf->Cell(140,$cellHigh,$title);

        //retour à la ligne
        $pdf->ln();
        $pdf->ln();

        /*
        * Texte d'intro
        */
        $pdf->SetFont('Arial','',9);
        $pdf->write($cellHigh,$texteFacture);


        //retour à la ligne
        $pdf->ln();
        $pdf->ln();


        /*
         * Tableau facture
         */
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(110,$cellHigh,'');
        $pdf->Cell(30,$cellHigh,'Date');
        $pdf->Cell(20,$cellHigh,'Montant');
        $pdf->ln();
        $pdf->SetFont('Arial','',9);


        foreach($facture->getCreances() as $creance)
        {
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(110,$cellHigh,$creance->getTitre(),'T');
            $pdf->SetFont('Arial','',9);
            //$pdf->Cell(30,$cellHigh,$creance->getDateCreation(),1);
            $pdf->Cell(30,$cellHigh,'date','T');
            $pdf->Cell(20,$cellHigh,number_format($creance->getMontantEmis(),2),'T');

            $pdf->ln();

            $remarque = $creance->getRemarque();
            if($remarque != null)
            {
                $pdf->Cell(20,$cellHigh,'Remarque:');
                $pdf->MultiCell(90,$cellHigh,$remarque);
            }
        }

        $i=1;
        foreach($facture->getRappels() as $rappel)
        {
            $pdf->Cell(110,$cellHigh,'Rappel '.$i,'T');
            $pdf->Cell(30,$cellHigh,'date','T');

            $pdf->Cell(20,$cellHigh,number_format($rappel->getFrais(),2),'T');
            $pdf->ln();
            $i++;
        }

        $pdf->Cell(110,$cellHigh,'','T');
        $pdf->Cell(30,$cellHigh,'Tolal:','T');
        $pdf->Cell(20,$cellHigh,number_format($facture->getMontantTotal(),2).' CHF',1);


        if($modePayement == 'BVR')
        {
            $pdf = $this->insertBvr($pdf,$adresse,$ccpBvr,$numeroReference,$affichageMontant,$montant);
        }
        elseif($modePayement == 'BV')
        {

        }
        else
        {

        }

        //$pdf->Output();
        //$pdf->getPdf()->Output('test.pdf','D');

        return $pdf;

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

    /*
     * Ajouter un BVR
     */
    /**
     * @param $pdf
     * @param $adresse
     * @param $ccp
     * @param $numeroReference
     * @param $affichageMontant
     * @param $montant
     * @return mixed
     */
    private function insertBvr($pdf,$adresse,$ccp,$numeroReference,$affichageMontant,$montant)
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
        $pdf->Line($xStart,$yStart,$xStart+5,$yStart);
        $pdf->Line($xStart+60,$yStart,$xStart+60,$yStart+5);
        $pdf->Line($xStart+205,$yStart,$xStart+210,$yStart);
        $pdf->Line($xStart+118,$yStart+80,$xStart+124,$yStart+80);
        $pdf->Line($xStart+121,$yStart+75,$xStart+121,$yStart+80);



        $cellWidth = 50;//ne sert pas vraiment
        $cellHigh = 4;

        $pdf->SetFont('Arial', '', 9);


        /*
         * Adresse récépissé
         */
        $x = $xStart + 5;
        $y = $yStart + 10;
        $pdf->SetXY($x,$y);
        $pdf->MultiCell($cellWidth,$cellHigh,$adresse);

        /*
         * compte récépissé
         */
        $x = $xStart + 28;
        $y = $yStart+42;
        $pdf->SetXY($x,$y);
        $pdf->Cell($cellWidth,$cellHigh,$ccp);

        /*
         * num. référence récépissé
         */
        $codeLine = $this->creatLineCode($numeroReference,'numRef');
        $x = $xStart + 5;
        $y = $yStart+60;
        $pdf->SetXY($x,$y);
        $pdf->Cell($cellWidth,$cellHigh,$codeLine);

        /*
         * Adresse virement
         */
        $x = $xStart + 65;
        $y = $yStart + 10;
        $pdf->SetXY($x,$y);
        $pdf->MultiCell($cellWidth,$cellHigh,$adresse);

        /*
         * compte virement
         */
        $x = $xStart + 89;
        $y = $yStart+42;
        $pdf->SetXY($x,$y);
        $pdf->Cell($cellWidth,$cellHigh,$ccp);

        /*
         * num. référance virement
         */
        $codeLine = $this->creatLineCode($numeroReference,'numRef');
        $x = $xStart + 130;
        $y = $yStart+38;
        $pdf->SetXY($x,$y);
        $pdf->Cell($cellWidth,$cellHigh,$codeLine);

        /*
         * code BVR en bas de coupon
         */
        $pdf->SetFont('Arial', '', 11);

        $codeLine = $this->creatLineCode($numeroReference,'code',$ccp);
        $x = $xStart+68;
        $y = $yStart+85;
        $pdf->SetXY($x,$y);
        $pdf->Cell($cellWidth,$cellHigh,$codeLine);

        if($affichageMontant == 'Oui')
        {
            /*
             * Montant sur le BVR
             */
            $pdf->SetFont('Arial', '', 9);

            $x = $xStart+50;
            $y = $yStart+50;
            $pdf->SetXY($x,$y);
            $pdf->Cell($cellWidth,$cellHigh,number_format($montant,2));

            $x = $xStart+90;
            $y = $yStart+50;
            $pdf->SetXY($x,$y);
            $pdf->Cell($cellWidth,$cellHigh,number_format($montant,2));
        }

        return $pdf;

    }

}