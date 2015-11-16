<?php

namespace AppBundle\Entity;

/**
 * Interface Expediable
 *
 * cette interface est desitinée au membres et familles
 *
 */
interface ExpediableInterface
{
    /**
     * Renvoi une adresse postale pour l'expedition par courrier
     *
     * @return mixed
     */
    public function getAdresseExpedition();

    /**
     * Renvoi une liste d'adresse email expediable.
     *
     * @return mixed
     */
    public function getListeEmailsExpedition();


}
