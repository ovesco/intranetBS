<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 17.11.16
 * Time: 10:58
 */

namespace AppBundle\Utils\Finances;

use AppBundle\Repository\FactureRepository;
use AppBundle\Entity\Payement;
use AppBundle\Entity\Facture;


class PayementCheck {

    /** @var  FactureRepository */
    private $factureRepository;

    public function __construct(FactureRepository $repository){

        $this->factureRepository = $repository;
    }

    /**
     * Cette fonction va checker le statut de lu payement en fonction des factures existantes.
     *
     * @param Payement $payement
     * @return Payement
     */
    public function check(Payement $payement){

        /** @var Facture $facture */
        $facture = $this->factureRepository->find($payement->getIdFacture());

        if($facture != Null)
        {
            if($facture->getStatut() == Facture::OPEN)
            {
                $montantTotalEmis = $facture->getMontantEmis();
                $montantRecu = $payement->getMontantRecu();

                if($montantTotalEmis == $montantRecu)
                {
                    $payement->setState(Payement::FOUND_VALID);
                }
                elseif($montantTotalEmis > $montantRecu)
                {
                    $payement->setState(Payement::FOUND_LOWER);
                }
                elseif($montantTotalEmis < $montantRecu)
                {
                    $payement->setState(Payement::FOUND_UPPER);
                }
                /*
                 * On lie le payement à la facture
                 */
                $payement->setFacture($facture);
                $facture->setPayement($payement);
                /*
                 * On definit la facture comme payée dans tout les cas...ce qui correspond à la réalité.
                 * par contre le payement reste à valider pour répartir la somme dans les créances
                 */
                $facture->setStatut(Facture::PAYED);
                $this->factureRepository->save($facture);
            }
            else
            {
                /*
                 * la facture a déjà été payée
                 */
                $payement->setState(Payement::FOUND_ALREADY_PAID);
            }
        }
        else
        {
            $payement->setState(Payement::NOT_FOUND);
        }

        return $payement;
    }


}