<?php

namespace Interne\FactureBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Interne\FichierBundle\Entity\Membre;
use Interne\FichierBundle\Entity\Famille;

/**
 * Creance
 *
 * @ORM\Table(name="facture_creances")
 * @ORM\Entity(repositoryClass="Interne\FactureBundle\Entity\CreanceRepository")
 */
class Creance
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
     * Une créance peut avoir un seul propriétaire...soit un membre...soit une famille.
     *
     * Les créances peut également appartenir à une facture une fois qu'elle sont "facturée"
     */

    /**
     * @var Membre
     *
     * @ORM\ManyToOne(targetEntity="Interne\FichierBundle\Entity\Membre", inversedBy="creances")
     * @ORM\JoinColumn(name="membre_id", referencedColumnName="id")
     */
    private $membre;

    /**
     * @var Famille
     *
     * @ORM\ManyToOne(targetEntity="Interne\FichierBundle\Entity\Famille", inversedBy="creances")
     * @ORM\JoinColumn(name="famille_id", referencedColumnName="id")
     */
    private $famille;

    /**
     * @var Facture
     *
     * @ORM\ManyToOne(targetEntity="Interne\FactureBundle\Entity\Facture", inversedBy="creances")
     * @ORM\JoinColumn(name="facture_id", referencedColumnName="id")
     */
    private $facture;

    /*
     * ========== VARIABLES =================
     */

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="remarque", type="text")
     */
    private $remarque;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="date")
     */
    private $dateCreation;

    /**
     * @var float
     *
     * @ORM\Column(name="montantEmis", type="float")
     */
    private $montantEmis;

    /**
     * @var float
     *
     * @ORM\Column(name="montantRecu", type="float")
     */
    private $montantRecu;

    /*
     * ========== Fonctions =================
     */


    public function __construct()
    {
        $this->setDateCreation(new \DateTime());
        $this->setMontantEmis(0);
        $this->setMontantRecu(0);
        $this->setRemarque('');
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
     * Set titre
     *
     * @param string $titre
     * @return Creance
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set remarque
     *
     * @param string $remarque
     * @return Creance
     */
    public function setRemarque($remarque)
    {
        $this->remarque = $remarque;

        return $this;
    }

    /**
     * Get remarque
     *
     * @return string 
     */
    public function getRemarque()
    {
        return $this->remarque;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Creance
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
     * Set montantEmis
     *
     * @param float $montantEmis
     * @return Creance
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
     * Set montantRecu
     *
     * @param float $montantRecu
     * @return Creance
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
     * Set facture
     *
     * @param Facture $facture
     * @return Creance
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
     * Set famille
     *
     * @param Famille $famille
     * @return Creance
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

    /*
     * Cette méthode regarde si la créance à une facture et si
     * cette facture est payée.
     */
    /**
     * Is payed
     *
     * @return Boolean
     */
    public function isPayed()
    {
        if($this->isFactured())
        {
            if($this->facture->getStatut() == 'payee')
            {
                return true;
            }
            else
                return false;
        }
        else
            return false;
    }

    /**
     * Is Factured
     *
     * @return Boolean
     */
    public function isFactured()
    {
        if($this->facture != null)
            return true;
        else
            return false;
    }

    /*
     * Cette fonction retourne le propriétaire de la créance.
     * Ce ne peut être que un membre ou une famille.
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
