<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Model
 *
 * @ORM\Table(name="app_model")
 * @ORM\Entity
 */
class Model
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
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\OneToOne(targetEntity="Fonction")
     * @ORM\JoinColumn(name="fonctionChef_id", referencedColumnName="id", nullable=true)
     */
    private $fonctionChef;


    /**
     * @ORM\ManyToMany(targetEntity="Fonction")
     * @ORM\JoinTable(name="app_model_fonction",
     *      joinColumns={@ORM\JoinColumn(name="model_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="fonction_id", referencedColumnName="id")}
     *      )
     *
     *
     */
    private $fonctions;



    /**
     * @var ArrayCollection 
     * 
     * @ORM\OneToMany(targetEntity="Groupe", mappedBy="type", cascade={"persist"})
     */
    private $groupes;

    /**
     * @var boolean
     * @ORM\Column(name="affichage_effectifs", type="boolean")
     */
    private $affichageEffectifs;


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
     * Set nom
     *
     * @param string $nom
     * @return Model
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    
        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groupes = new ArrayCollection();
        $this->fonctions = new ArrayCollection();
    }

    /**
     * Add groupes
     *
     * @param Groupe $groupes
     * @return Model
     */
    public function addGroupe(Groupe $groupes)
    {
        $this->groupes[] = $groupes;

        return $this;
    }

    /**
     * Remove groupes
     *
     * @param Groupe $groupes
     */
    public function removeGroupe(Groupe $groupes)
    {
        $this->groupes->removeElement($groupes);
    }

    /**
     * Get groupes
     *
     * @return ArrayCollection
     */
    public function getGroupes()
    {
        return $this->groupes;
    }

    /**
     * Set fonctionChef
     *
     * @param Fonction $fonctionChef
     * @return Model
     */
    public function setFonctionChef(Fonction $fonctionChef = null)
    {
        $this->fonctionChef = $fonctionChef;

        return $this;
    }

    /**
     * Get fonctionChef
     *
     * @return Fonction
     */
    public function getFonctionChef()
    {
        return $this->fonctionChef;
    }

    /**
     * Set affichageEffectifs
     *
     * @param boolean $affichageEffectifs
     * @return Groupe
     */
    public function setAffichageEffectifs($affichageEffectifs)
    {
        $this->affichageEffectifs = $affichageEffectifs;

        return $this;
    }

    /**
     * Is affichageEffectifs
     *
     * @return boolean
     */
    public function isAffichageEffectifs()
    {
        return $this->affichageEffectifs;
    }

    /**
     * Get affichageEffectifs
     *
     * @return boolean
     */
    public function getAffichageEffectifs()
    {
        return $this->affichageEffectifs;
    }

    /**
     * @return String
     */
    public function __toString() {
        return $this->getNom();
    }

    /**
     * Add fonction
     *
     * @param Fonction $fonction
     * @return Model
     */
    public function addFonction(Fonction $fonction)
    {
        $this->fonctions[] = $fonction;

        return $this;
    }

    /**
     * Remove fonction
     *
     * @param Fonction $fonction
     */
    public function removeFonction(Fonction $fonction)
    {
        $this->groupes->removeElement($fonction);
    }

    /**
     * Get fonctions
     *
     * @return ArrayCollection
     */
    public function getFonctions()
    {
        return $this->fonctions;
    }

    /**
     * Set fonctions
     *
     * @param $fonctions
     */
    public function setFonctions($fonctions)
    {
        $this->fonctions = $fonctions;
    }

}
