<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Geniteur;

//FinancesBundle
use Interne\FinancesBundle\Entity\Creance;
use Interne\FinancesBundle\Entity\Facture;

/**
 * Famille
 * @ORM\Entity
 * @ORM\Table(name="app_familles")
 */
 
class Famille
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Membre", mappedBy="famille", cascade={"persist", "remove"}, fetch="EAGER")
     */
    private $membres;
    
	
    /**
     * @orm\OneToOne(targetEntity="AppBundle\Entity\Adresse", cascade={"persist", "remove"}, fetch="EAGER")
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", nullable=true)
     */
    protected $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    protected $email;


    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Geniteur", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(name="pere_id", referencedColumnName="id")
     */
    private $pere;

    /**
     * 
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Geniteur", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(name="mere_id", referencedColumnName="id")
     */
    private $mere;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min = "2")
     */
    private $nom;

    /**
     * @var integer
     *
     * @ORM\Column(name="validity", type="integer")
     */
    private $validity;



    /*
     * ====== FinancesBundle =======
     */
    /**
     * @var ArryCollection
     *
     * @ORM\OneToMany(targetEntity="Interne\FinancesBundle\Entity\Facture",
     *                mappedBy="famille", cascade={"persist"})
     */
    private $factures;

    /**
     * @var ArryCollection
     *
     * @ORM\OneToMany(targetEntity="Interne\FinancesBundle\Entity\Creance",
     *                mappedBy="famille", cascade={"persist"})
     */
    private $creances;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->membres = new ArrayCollection();

        /*
         * FinancesBundle
         */
        $this->creances = new ArrayCollection();
        $this->factures = new ArrayCollection();
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
     * Get self
     * @return famille
     */
    public function getSelf() {

        return $this;
    }


    /**
     * Get membres
     *
     * @return array
     */
    public function getMembres()
    {
        return $this->membres;
    }

    /**
     * Set pere
     *
     * @param \AppBundle\Entity\Geniteur
     * @return Famille
     */
    public function setPere( \AppBundle\Entity\Geniteur $pere = null)
    {
        $this->pere = $pere;

        return $this;
    }

    /**
     * Get pere
     *
     * @return \AppBundle\Entity\Geniteur
     */
    public function getPere()
    {
        return $this->pere;
    }

    /**
     * Set mere
     *
     * @param  \AppBundle\Entity\Geniteur
     * @return Famille
     */
    public function setMere( \AppBundle\Entity\Geniteur $mere = null)
    {
        $this->mere = $mere;

        return $this;
    }

    /**
     * Get mere
     *
     * @return \AppBundle\Entity\Geniteur
     */
    public function getMere()
    {
        return $this->mere;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Famille
     */
    public function setNom($nom)
    {
        $this->nom = ucwords($nom);

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return ucwords($this->nom);
    }


    /**
     * Add membres
     *
     * @param \AppBundle\Entity\Membre $membres
     * @return Famille
     */
    public function addMembre(\AppBundle\Entity\Membre $membres)
    {
        $this->membres[] = $membres;
    	$membres->setFamille($this);
        return $this;
    }

    /**
     * Remove membres
     *
     * @param \AppBundle\Entity\Membre $membres
     */
    public function removeMembre(\AppBundle\Entity\Membre $membres)
    {
        $this->membres->removeElement($membres);
    }

    /**
     * Set adresse
     *
     * @param \AppBundle\Entity\Adresse $adresse
     * @return Famille
     */
    public function setAdresse(\AppBundle\Entity\Adresse $adresse = null)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return \AppBundle\Entity\Adresse
     */
    public function getAdressePrincipale()
    {
        $potentiel = null;

        $adresses = array(
            'famille' => $this->getAdresse(),
            'mere' => ($this->getMere() == null) ? null : $this->getMere()->getAdresse(),
            'pere' => ($this->getPere() == null) ? null : $this->getPere()->getAdresse()
        );

        foreach($adresses as $k => $adresse) {

            if(!is_null($adresse)) {

                if ($adresse->getFacturable() == true) {

                    return array('adresse' => $adresse, 'origine' => $k,
                        'owner' => array(
                            'nom' => $this->getNom(),
                            'class' => 'Famille',
                    ));
                }

                if ($potentiel == null)
                    $potentiel = array('adresse' => $adresse, 'origine' => $k,
                        'owner' => array(
                            'nom' => $this->getNom(),
                            'class' => 'Famille',));
            }

        }

        return $potentiel;
    }

    public function getAdresse() {

        return $this->adresse;
    }

    /**
     * Doit renvoyer quelque chose qui permet d'identifier (humainement) une famille
     * Le nom n'est pas suffisant p.ex puisqu'il peut y avoir plusieurs famille avec le même nom
     *
     * @return string
     */
    public function __toString() {
        return "Les " . $this->getNom() . " de " . $this->getAdresse()->getLocalite(); // . " (" . sizeof($this->getMembres()) . ")";
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return Famille
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string 
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Famille
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set validity
     *
     * @param integer $validity
     * @return Famille
     */
    public function setValidity($validity)
    {
        $this->validity = $validity;

        return $this;
    }

    /**
     * Get validity
     *
     * @return integer 
     */
    public function getValidity()
    {
        return $this->validity;
    }

    /*
     * ====== FinancesBundle =======
     */

    /**
     * Is classe
     *
     * @param string $className
     * @return boolean
     */
    public function isClass($className)
    {
        if($className == 'Famille')
            return true;
        else
            return false;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return 'Famille';
    }

    /**
     * Set facture
     *
     * @param ArrayCollection $factures
     * @return Famille
     */
    public function setFacture(ArrayCollection $factures)
    {
        $this->factures = $factures;

        foreach($factures as $facture)
        {
            $facture->setFamille($this);
        }

        return $this;
    }

    /**
     * Get facture
     *
     * @return ArrayCollection
     */
    public function getFactures()
    {
        return $this->factures;
    }

    /**
     * Add facture
     *
     * @param Facture $facture
     * @return Famille
     */
    public function addFacture($facture)
    {
        $this->factures[] = $facture;
        $facture->setFamille($this);

        return $this;
    }

    /**
     * Remove facture
     *
     * @param Facture $facture
     * @return Famille
     */
    public function removeFacture($facture)
    {
        $this->factures->remove($facture);
        $facture->setFamille(null);

        return $this;
    }

    /**
     * Add creance
     *
     * @param Creance $creance
     * @return Famille
     */
    public function addCreance($creance)
    {
        $this->creances[] = $creance;
        $creance->setFamille($this);

        return $this;
    }

    /**
     * Remove creance
     *
     * @param Creance $creance
     * @return Famille
     */
    public function removeCreance($creance)
    {
        $this->creances->remove($creance);
        $creance->setFamille(null);

        return $this;
    }

    /**
     * Set creances
     *
     * @param ArrayCollection $creances
     * @return Famille
     */
    public function setCreances(ArrayCollection $creances)
    {
        $this->creances = $creances;

        foreach($creances as $creance)
        {
            $creance->setFamille($this);
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



}
