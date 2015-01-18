<?php

namespace Interne\FinancesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Membre;

/**
 * Class CreanceToMembre
 * @package Interne\FinancesBundle\Entity
 * @ORM\Entity(repositoryClass="Interne\FinancesBundle\Entity\CreanceToMembreRepository")
 *
 */
class CreanceToMembre extends Creance
{
    /**
     * @var Membre
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Membre", inversedBy="creances")
     * @ORM\JoinColumn(name="membre_id", referencedColumnName="id")
     */
    private $membre;

    /**
     * Set membre
     *
     * @param Membre $membre
     * @return Creance
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

    function loadFromCreance(Creance $creance)
    {
        $this->facture = $creance->facture;
        $this->titre = $creance->titre;
        $this->remarque = $creance->remarque;
        $this->montantEmis = $creance->montantEmis;
        $this->montantRecu = $creance->montantRecu;
        $this->dateCreation = $creance->dateCreation;
    }
}