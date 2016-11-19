<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 18.11.16
 * Time: 17:32
 */

namespace AppBundle\Utils\Finances;

use AppBundle\Repository\FactureRepository;
use AppBundle\Entity\Facture;
use AppBundle\Entity\Creance;
use AppBundle\Entity\Rappel;

/**
 * permet de répartire l'argent dans recu pour une facture de facon automatique dans ses créance et rappel
 *
 * Class FactureAutoDistribution
 * @package AppBundle\Utils\Finances
 */
class FactureAutoDistribution {

    /** @var  FactureRepository */
    private $factureRepository;


    /**
     * @param FactureRepository $factureRepository
     */
    public function __construct(FactureRepository $factureRepository){

        $this->factureRepository = $factureRepository;
    }

    /**
     * Repartit la somme dans les créances et les rappels en partant du principe que la somme est correct
     *
     * @param Facture $facture
     */
    public function distributEqual(Facture $facture)
    {
        /** @var Creance $creance */
        foreach($facture->getCreances() as $creance)
        {
            $creance->setMontantRecu($creance->getMontantEmis());
        }

        /** @var Rappel $rappel */
        foreach($facture->getRappels() as $rappel)
        {
            $rappel->setMontantRecu($rappel->getMontantEmis());
        }

        $this->factureRepository->save($facture);
    }

    /**
     *
     * Repartit la somme dans les créances et les rappels
     * en respectant le ration de différence entre
     * la somme emise et la somme recu
     *
     * @param Facture $facture
     * @param $montantRecu
     */
    public function distribut(Facture $facture, $montantRecu)
    {
        $totalEmis = $facture->getMontantEmis();

        $ratio = $montantRecu/$totalEmis;

        /** @var Creance $creance */
        foreach($facture->getCreances() as $creance)
        {
            $creance->setMontantRecu($creance->getMontantEmis()*$ratio);
        }

        /** @var Rappel $rappel */
        foreach($facture->getRappels() as $rappel)
        {
            $rappel->setMontantRecu($rappel->getMontantEmis()*$ratio);
        }

        /*
         * On verifie ensuite la somme pour que ca corresponde
         */
        $computedMontantRecu = $facture->getMontantRecu();

        $delta = $computedMontantRecu - $montantRecu;

        /** @var Creance $fistCreance */
        $fistCreance = $facture->getCreances()->first();

        /* on corrige le résultat sur la somme de la premiere créance...c'est un peu arbitraire mais comme ca.*/
        $fistCreance->setMontantRecu($fistCreance->getMontantRecu() - $delta);

        /* on sauve */
        $this->factureRepository->save($facture);
    }


}