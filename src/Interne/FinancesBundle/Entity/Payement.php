<?php

namespace Interne\FinancesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\ElasticaBundle\Configuration\Search;

/**
 * Payement
 *
 * @ORM\Table(name="finances_bundle_payement")
 * @ORM\Entity
 * @Search(repositoryClass="Interne\FinancesBundle\SearchRepository\PayementRepository")
 */
class Payement
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="idFacture", type="integer")
     */
    private $idFacture; //n'est pas forcement représentatif d'un ID existant. (state: not_found)

    /**
     * @var Facture
     *
     * @ORM\OneToOne(targetEntity="Facture", inversedBy="payement", cascade={"persist"})
     */
    private $facture;

    /**
     * @var float
     *
     * @ORM\Column(name="montantRecu", type="float")
     */
    private $montantRecu;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", columnDefinition="ENUM('not_found','found_payed','found_valid','found_lower','found_lower_new_facture','found_upper','waiting')")
     */
    private $state;

    /**
     * @param  $idFacture
     * @param  $montantRecu
     * @param  $date
     * @param  $state
     */
    public function __construct($idFacture, $montantRecu, $date, $state)
    {
        $this->idFacture = $idFacture;
        $this->montantRecu = $montantRecu;
        $this->date = $date;
        $this->state = $state;
    }


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
     * Set idFacture
     *
     * @param integer $idFacture
     * @return Payement
     */
    public function setIdFacture($idFacture)
    {
        $this->idFacture = $idFacture;

        return $this;
    }

    /**
     * Get idFacture
     *
     * @return integer 
     */
    public function getIdFacture()
    {
        return $this->idFacture;
    }

    /**
     * Set montantRecu
     *
     * @param float $montantRecu
     * @return Payement
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
     * Set date
     *
     * @param \DateTime $date
     * @return Payement
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
     * Set state
     *
     * @param string $state
     * @return Payement
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set facture
     *
     * @param \Interne\FinancesBundle\Entity\Facture $facture
     *
     * @return Payement
     */
    public function setFacture(\Interne\FinancesBundle\Entity\Facture $facture = null)
    {
        $this->facture = $facture;

        return $this;
    }

    /**
     * Get facture
     *
     * @return \Interne\FinancesBundle\Entity\Facture
     */
    public function getFacture()
    {
        return $this->facture;
    }
}
