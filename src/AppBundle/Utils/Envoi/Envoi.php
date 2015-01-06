<?php

namespace AppBundle\Utils\Envoi;

use AppBundle\Utils\Export\Pdf;
use Doctrine\ORM\EntityManager;


/**
 * Les envois sont géré depuis la classe ListeEnvoi.php
 *
 * Class Envoi
 * @package AppBundle\Utils\Envoi
 */
class Envoi {

    /**
     * Contient le Membre/Famille à qui l'envoi est destiné.
     * @var integer
     */
    public $ownerId;

    /**
     * Contient le Membre/Famille à qui l'envoi est destiné.
     * @var string
     */
    public $ownerClass;


    /**
     * Contient le document à envoyer (sans l'adresse, elle sera ajoutée plus tard)
     * @var Pdf
     */
    public $documentPDF;

    /**
     * Contient une breve description de l'envoie (ex: Facture, document)
     * @var String
     */
    public $description;

    /**
     * Retourne le token de l'envoi.
     * Le token est géneré par md5
     */
    public function getToken() {

        /*
         * De cette facon, un doublon aurait le même token et l'envoi sera donc ajouter/envoyé qu'une fois.
         */
        return md5($this->ownerId.$this->ownerClass.$this->description);
    }

    public function __construct($ownerId,$ownerClass,Pdf $pdf,$description,EntityManager $em) {

        $this->ownerId = $ownerId;
        $this->ownerClass = $ownerClass;
        $this->documentPDF = $pdf;
        $this->description = $description;

    }

}