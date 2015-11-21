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
     * @param $debiteur
     * @return mixed
     */
    public function setDebiteur($debiteur);

    /**
     * @return Debiteur
     */
    public function getDebiteur();

    /**
     * @param Creance $creance
     * @return mixed
     */
    public function addCreance($creance);

    /**
     * @param Facture $facture
     * @return mixed
     */
    public function addFacture($facture);


}
