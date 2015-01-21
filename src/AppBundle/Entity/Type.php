<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Type
 *
 * @ORM\Table(name="app_types")
 * @ORM\Entity
 */
class Type
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
     * @ORM\JoinColumn(name="fonction_id", referencedColumnName="id", nullable=true)
     */
    private $fonctionChef;
    
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
     * @return Type
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
        $this->groupes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add groupes
     *
     * @param \AppBundle\Entity\Groupe $groupes
     * @return Type
     */
    public function addGroupe(\AppBundle\Entity\Groupe $groupes)
    {
        $this->groupes[] = $groupes;

        return $this;
    }

    /**
     * Remove groupes
     *
     * @param \AppBundle\Entity\Groupe $groupes
     */
    public function removeGroupe(\AppBundle\Entity\Groupe $groupes)
    {
        $this->groupes->removeElement($groupes);
    }

    /**
     * Get groupes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroupes()
    {
        return $this->groupes;
    }

    /**
     * Set fonctionChef
     *
     * @param \AppBundle\Entity\Fonction $fonctionChef
     * @return Type
     */
    public function setFonctionChef(\AppBundle\Entity\Fonction $fonctionChef = null)
    {
        $this->fonctionChef = $fonctionChef;

        return $this;
    }

    /**
     * Get fonctionChef
     *
     * @return \AppBundle\Entity\Fonction 
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
     * @return String
     */
    public function __toString() {
        return $this->getNom();
    }
}
