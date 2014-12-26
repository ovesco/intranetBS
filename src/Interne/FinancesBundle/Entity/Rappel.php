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
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var float
     *
     * @ORM\Column(name="frais", type="float")
     */
    private $frais;

    /**
     * @var float
     *
     * @ORM\Column(name="montantRecu", type="float")
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
     * Set frais
     *
     * @param float $frais
     * @return Rappel
     */
    public function setFrais($frais)
    {
        $this->frais = $frais;

        return $this;
    }

    /**
     * Get frais
     *
     * @return float
     */
    public function getFrais()
    {
        return $this->frais;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Rappel
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
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
}
