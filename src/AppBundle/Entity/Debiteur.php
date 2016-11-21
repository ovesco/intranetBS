<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Debiteur
 *
 * @ORM\Table(name="app_debiteur")
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="proprietaire", type="string")
 * @ORM\DiscriminatorMap({"membre" = "DebiteurMembre", "famille" = "DebiteurFamille"})
 */
abstract class Debiteur
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    abstract function getOwner();

    /**
     * @return string
     */
    abstract function getOwnerAsString();

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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Creance",
     *                mappedBy="debiteur", cascade={"persist","remove"})
     */
    private $creances;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Facture",
     *                mappedBy="debiteur", cascade={"persist","remove"})
     */
    private $factures;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->creances = new \Doctrine\Common\Collections\ArrayCollection();
        $this->factures = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add creance
     *
     * @param \AppBundle\Entity\Creance $creance
     *
     * @return Debiteur
     */
    public function addCreance(\AppBundle\Entity\Creance $creance)
    {
        $this->creances[] = $creance;
        $creance->setDebiteur($this);

        return $this;
    }

    /**
     * Remove creance
     *
     * @param \AppBundle\Entity\Creance $creance
     */
    public function removeCreance(\AppBundle\Entity\Creance $creance)
    {
        $this->creances->removeElement($creance);
    }

    /**
     * Get creances
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCreances()
    {
        return $this->creances;
    }

    /**
     * Add facture
     *
     * @param \AppBundle\Entity\Facture $facture
     *
     * @return Debiteur
     */
    public function addFacture(\AppBundle\Entity\Facture $facture)
    {
        $this->factures[] = $facture;
        $facture->setDebiteur($this);
        return $this;
    }

    /**
     * Remove facture
     *
     * @param \AppBundle\Entity\Facture $facture
     */
    public function removeFacture(\AppBundle\Entity\Facture $facture)
    {
        $this->factures->removeElement($facture);
    }

    /**
     * Get factures
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFactures()
    {
        return $this->factures;
    }
}
