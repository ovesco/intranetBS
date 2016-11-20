<?php


namespace AppBundle\Entity;

/**
 *
 *
 * cette interface est desitinée au membres et familles
 *
 */
interface DebiteurInterface
{
    /**
     * @param Creance $creance
     * @return mixed
     */
    public function addCreance(Creance $creance);

    /**
     * @param Facture $facture
     * @return mixed
     */
    public function addFacture(Facture $facture);

}
