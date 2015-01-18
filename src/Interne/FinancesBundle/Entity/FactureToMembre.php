<?php

namespace Interne\FinancesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Membre;
use AppBundle\Entity\Adresse;


/**
 * Class FactureToMembre
 * @package Interne\FinancesBundle\Entity
 * @ORM\Entity(repositoryClass="Interne\FinancesBundle\Entity\FactureToMembreRepository")
 */
class FactureToMembre extends Facture
{
    /**
     * @var Membre
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Membre", inversedBy="factures")
     * @ORM\JoinColumn(name="membre_id", referencedColumnName="id")
     */
    private $membre;

    /**
     * Set membre
     *
     * @param Membre $membre
     * @return Facture
     */
    public function setMembre($membre)
    {
        $this->membre = $membre;

        return $this;
    }

    /**
     * Get membre
     *
     * @return Membre
     */
    public function getMembre()
    {
        return $this->membre;
    }

    /**
     * Get owner
     */
    public function getOwner()
    {
        return $this->membre;
    }

    /**
     * Get ownerAdresse
     *
     * @return Adresse
     */
    public function getOwnerAdresse()
    {
        return $this->membre->getAdressePrincipale();
    }
}