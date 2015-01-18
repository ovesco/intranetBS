<?php

namespace Interne\FinancesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payement
 *
 * @ORM\Table(name="finances_bundle_payements")
 * @ORM\Entity(repositoryClass="Interne\FinancesBundle\Entity\PayementRepository")
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
     * @var float
     *
     * @ORM\Column(name="montantRecu", type="float")
     */
    private $montantRecu;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datePayement", type="datetime")
     */
    private $datePayement;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", columnDefinition="ENUM('not_found','found_lower_valid','found_lower_new_facture','found_upper','waiting')")
     */
    private $state;

    /**
     * @param $idFacture
     * @param  $montantRecu
     * @param  $datePayement
     * @param  $state
     */
    public function __construct($idFacture, $montantRecu, $datePayement, $state)
    {
        $this->idFacture = $idFacture;
        $this->montantRecu = $montantRecu;
        $this->datePayement = $datePayement;
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
     * Set datePayement
     *
     * @param \DateTime $datePayement
     * @return Payement
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
}
