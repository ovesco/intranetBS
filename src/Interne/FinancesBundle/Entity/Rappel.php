<?php

namespace Interne\FinancesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rappel
 *
 * @ORM\Table(name="finances_bundle_rappels")
 * @ORM\Entity
 */
class Rappel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /*
     * =========== RELATIONS ===============
     */

    /**
     * @var Facture
     *
     * @ORM\ManyToOne(targetEntity="Interne\FinancesBundle\Entity\Facture", inversedBy="rappels")
     * @ORM\JoinColumn(name="facture_id", referencedColumnName="id")
     */
    private $facture;

    /*
     * ========== VARIABLES =================
     */

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="date")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datePayement", type="date", nullable=true)
     */
    private $datePayement;

    /**
     * @var float
     *
     * @ORM\Column(name="montantEmis", type="float", nullable=true)
     */
    private $montantEmis;

    /**
     * @var float
     *
     * @ORM\Column(name="montantRecu", type="float", nullable=true)
     */
    private $montantRecu;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set montantEmis
     *
     * @param float $montantEmis
     * @return Rappel
     */
    public function setMontantEmis($montantEmis)
    {
        $this->montantEmis = $montantEmis;

        return $this;
    }

    /**
     * Get montantEmis
     *
     * @return float
     */
    public function getMontantEmis()
    {
        return $this->montantEmis;
    }

    /**
     * Set facture
     *
     * @param Facture $facture
     * @return Rappel
     */
    public function setFacture($facture)
    {
        $this->facture = $facture;

        return $this;
    }

    /**
     * Get facture
     *
     * @return Facture
     */
    public function getFacture()
    {
        return $this->facture;
    }

    /**
     * Set montantRecu
     *
     * @param float $montantRecu
     * @return Rappel
     */
    public function setMontantRecu($montantRecu)
    {
        $this->montantRecu = $montantRecu;

        return $this;
    }

    /**
     * Get montantRecu
     *
     * @return float
     */
    public function getMontantRecu()
    {
        return $this->montantRecu;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Rappel
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set datePayement
     *
     * @param \DateTime $datePayement
     * @return Rappel
     */
    public function setDatePayement($datePayement)
    {
        $this->datePayement = $datePayement;

        return $this;
    }

    /**
     * Get datePayement
     *
     * @return \DateTime
     */
    public function getDatePayement()
    {
        return $this->datePayement;
    }

}
