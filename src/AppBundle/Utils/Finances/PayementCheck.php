<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 17.11.16
 * Time: 10:58
 */

namespace AppBundle\Utils\Finances;

use AppBundle\Repository\FactureRepository;
use AppBundle\Repository\PayementRepository;
use AppBundle\Entity\Payement;
use AppBundle\Entity\Facture;


class PayementCheck {

    /** @var  FactureRepository */
    private $factureRepository;

    /** @var  PayementRepository */
    private $payementRepository;

    /** @var  FactureAutoDistribution */
    private $autoDistribution;

    /**
     * @param FactureRepository $factureRepository
     * @param PayementRepository $payementRepository
     */
    public function __construct(FactureRepository $factureRepository, PayementRepository $payementRepository, FactureAutoDistribution $autoDistribution){

        $this->factureRepository = $factureRepository;
        $this->payementRepository = $payementRepository;
        $this->autoDistribution = $autoDistribution;
    }

    /**
     * Cette fonction va checker le statut du payement en fonction des factures existantes.
     *
     * @param Payement $payement
     * @return Payement
     */
    public function validation(Payement $payement){

        /** @var Facture $facture */
        $facture = $this->factureRepository->find($payement->getIdFacture());

        /*
         * On vérifie que la facture existe bien.
         */
        if($facture instanceof Facture)
        {
            /*
             * On vérifie que aucun payement n'a été recu avant...
             */
            if(!$facture->hasPayement())
            {
                $montantTotalEmis = $facture->getMontantEmis();
                $montantRecu = $payement->getMontantRecu();

                if($montantTotalEmis == $montantRecu)
                {
                    /*
                     * Le payement est accépté
                     */
                    $payement->setState(Payement::FOUND_VALID);
                    $payement->setValidated(true);
                    $facture->setStatut(Facture::PAYED);
                    $facture->setDatePayement($payement->getDate());
                    /*
                     * répartition des montants
                     */
                    $this->autoDistribution->distributEqual($facture,$montantRecu);
                }
                elseif($montantTotalEmis > $montantRecu)
                {
                    /*
                     * Le payement n'est pas accepté...il passera par la validation manuelle
                     * par contre la facture est mise en statut payé
                     */
                    $payement->setState(Payement::FOUND_LOWER);
                    $payement->setValidated(false);
                    $facture->setStatut(Facture::PAYED);


                    $this->autoDistribution->distribut($facture,$montantRecu);
                }
                elseif($montantTotalEmis < $montantRecu)
                {
                    /*
                     * Le payement est accépté
                     */
                    $payement->setState(Payement::FOUND_UPPER);
                    $payement->setValidated(true);
                    $facture->setStatut(Facture::PAYED);
                    $facture->setDatePayement($payement->getDate());
                    /*
                     * répartition des montants
                     */
                    $this->autoDistribution->distribut($facture,$montantRecu);
                }
                /*
                 * On lie le payement à la facture
                 */
                $payement->setFacture($facture);
                $facture->setPayement($payement);

                $this->factureRepository->save($facture);
            }
            else
            {
                /*
                 * la facture a déjà été payée
                 */
                $payement->setState(Payement::FOUND_ALREADY_PAID);
                $payement->setValidated(false);
            }
        }
        else
        {
            /*
             * la facture n'est pas trouvée.
             */
            $payement->setState(Payement::NOT_FOUND);
            $payement->setValidated(false);
        }



        $this->payementRepository->save($payement);
    }


}