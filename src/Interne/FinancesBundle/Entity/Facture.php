<?php

namespace Interne\FinancesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Membre;
use AppBundle\Entity\Famille;
use AppBundle\Entity\Adresse;

/**
 * Facture
 *
 * @ORM\Table(name="finances_bundle_factures")
 * @ORM\Entity(repositoryClass="Interne\FinancesBundle\Entity\FactureRepository")
 */
class Facture
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
     *
     * Une facture contient une liste de cérances et une liste de rappels.
     *
     * Une facture à un propriétaire, soit un membre, soit une famille.
     */


    /**
     * @var ArryCollection
     *
     * @ORM\OneToMany(targetEntity="Interne\FinancesBundle\Entity\Rappel",
     *                mappedBy="facture", cascade={"persist", "remove"})
     */
    private $rappels;

    /**
     * @var ArryCollection
     *
     * @ORM\OneToMany(targetEntity="Interne\FinancesBundle\Entity\Creance",
     *                mappedBy="facture", cascade={"persist", "remove"})
     */
    private $creances;

    /**
     * @var Membre
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Membre", inversedBy="factures")
     * @ORM\JoinColumn(name="membre_id", referencedColumnName="id")
     */
    private $membre;

    /**
     * @var Famille
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Famille", inversedBy="factures")
     * @ORM\JoinColumn(name="famille_id", referencedColumnName="id")
     */
    private $famille;

    /*
     * ========== VARIABLES =================
     */

    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", length=255)
     */
    private $statut;

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


    /*
     * ============= FONCTIONS ============
     */

    public function __construct()
    {
        $this->rappels = new ArrayCollection();
        $this->statut = 'ouverte';

        $this->creances = new ArrayCollection();


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
     * Set id
     *
     * @param integer $id
     * @return Facture
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
    * Set statut
    *
    * @param string $statut
    * @return Facture
    */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return string
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Add rappel
     *
     * @param Rappel $rappel
     * @return Facture
     */
    public function addRappel($rappel)
    {
        $this->rappels[] = $rappel;
        $rappel->setFacture($this);

        return $this;
    }

    /**
     * Remove rappel
     *
     * @param Rappel $rappel
     * @return Facture
     */
    public function removeRappel($rappel)
    {
        $this->rappels->remove($rappel);
        $rappel->setFacture(null);

        return $this;
    }

    /**
     * Set rappels
     *
     * @param ArrayCollection $rappels
     * @return Facture
     */
    public function setRappels(ArrayCollection $rappels)
    {
        $this->rappels = $rappels;

        foreach($rappels as $rappel)
        {
            $rappel->setFacture($this);
        }

        return $this;
    }

    /**
     * Get rappels
     *
     * @return ArrayCollection
     */
    public function getRappels()
    {
        return $this->rappels;
    }


    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Facture
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
     * Get montantEmis
     *
     * @return float
     */
    public function getMontantEmis()
    {

        return $this->getMontantEmisCreances() + $this->getMontantEmisRappels();
    }

    /**
     * Get montantEmisCreances
     *
     * @return float
     */
    public function getMontantEmisCreances()
    {
        $montantCreances = 0;
        foreach($this->creances as $creance)
        {
            $montantCreances = $montantCreances + $creance->getMontantEmis();
        }
        return $montantCreances;
    }

    /**
     * Get montantEmisRappels
     *
     * @return float
     */
    public function getMontantEmisRappels()
    {
        $montantRappel = 0;
        foreach($this->rappels as $rappel)
        {
            $montantRappel = $montantRappel + $rappel->getMontantEmis();
        }
        return $montantRappel;
    }

    /**
     * Get montantRecu
     *
     * @return float
     */
    public function getMontantRecu()
    {

        return $this->getMontantRecuCreances() + $this->getMontantRecuRappels();
    }

    /**
     * Get montantRecuCreances
     *
     * @return float
     */
    public function getMontantRecuCreances()
    {
        $montantCreances = 0;
        foreach($this->creances as $creance)
        {
            $montantCreances = $montantCreances + $creance->getMontantRecu();
        }
        return $montantCreances;
    }

    /**
     * Get montantRecuRappels
     *
     * @return float
     */
    public function getMontantRecuRappels()
    {
        $montantRappel = 0;
        foreach($this->rappels as $rappel)
        {
            $montantRappel = $montantRappel + $rappel->getMontantRecu();
        }
        return $montantRappel;
    }


    /**
     * Set datePayement
     *
     * @param \DateTime $datePayement
     * @return Facture
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
     * Get nombreRappels
     *
     * @return integer
     */
    public function getNombreRappels()
    {
        return $this->rappels->count();
    }

    /**
     * Add creance
     *
     * @param Creance $creance
     * @return Facture
     */
    public function addCreance($creance)
    {
        $this->creances[] = $creance;
        $creance->setFacture($this);

        return $this;
    }

    /**
     * Remove creance
     *
     * @param Creance $creance
     * @return Facture
     */
    public function removeCreance($creance)
    {
        $this->creances->remove($creance);
        $creance->setFacture(null);

        return $this;
    }

    /**
     * Set creances
     *
     * @param ArrayCollection $creances
     * @return Facture
     */
    public function setCreances(ArrayCollection $creances)
    {
        $this->creances = $creances;

        foreach($creances as $creance)
        {
            $creance->setFacture($this);
        }

        return $this;
    }

    /**
     * Get creances
     *
     * @return ArrayCollection
     */
    public function getCreances()
    {
        return $this->creances;
    }

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
     * Get ownerAdresse
     *
     * @return Adresse
     */
    public function getOwnerAdresse()
    {

        if($this->membre != null)
        {
            return $this->membre->getAdressePrincipale();
        }
        elseif($this->famille != null)
        {
            return $this->famille->getAdresse();
        }
        else
            return null;
    }

    /*
     * La facture peut avoir soit un membre, soit une famille comme prorpiétaire.
     */
    /**
     * Get owner
     */
    public function getOwner()
    {
        if($this->membre != null)
            return $this->membre;
        elseif($this->famille != null)
            return $this->famille;
        else
            return null;
    }





}
