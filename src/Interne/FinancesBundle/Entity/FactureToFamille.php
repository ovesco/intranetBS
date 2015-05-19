<?php

namespace Interne\FinancesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Famille;
use AppBundle\Entity\Adresse;
use FOS\ElasticaBundle\Configuration\Search;

/**
 * Class FactureToFamille
 * @package Interne\FinancesBundle\Entity
 * @ORM\Entity
 * @Search(repositoryClass="Interne\FinancesBundle\SearchRepository\FactureToFamilleRepository")
 */
class FactureToFamille extends Facture
{
    /**
     * @var Famille
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Famille", inversedBy="factures")
     * @ORM\JoinColumn(name="famille_id", referencedColumnName="id")
     */
    private $famille;

    /**
     * Set famille
     *
     * @param Famille $famille
     * @return Facture
     */
    public function setFamille($famille)
    {
        $this->famille = $famille;

        return $this;
    }

    /**
     * Get famille
     *
     * @return Famille
     */
    public function getFamille()
    {
        return $this->famille;
    }

    /**
     * Get owner
     */
    public function getOwner()
    {
        return $this->famille;
    }

    /**
     * Get ownerAdresse
     *
     * @return Adresse
     */
    public function getOwnerAdresse()
    {
        return $this->famille->getAdressePrincipale();
    }
}