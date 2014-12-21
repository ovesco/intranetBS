<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fonction
 *
 * @ORM\Table(name="app_fonctions")
 * @ORM\Entity
 */
class Fonction
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
     * @var string
     *
     * @ORM\Column(name="abreviation", type="string", length=255)
     */
    private $abreviation;

    /**
     * @var ArrayCollection 
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Attribution", mappedBy="fonction")
     */
    private $attributions;


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
     * @return Fonction
     */
    public function setNom($nom)
    {
        $this->nom = ucfirst($nom);

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return ucfirst($this->nom);
    }

    /**
     * Set abreviation
     *
     * @param string $abreviation
     * @return Fonction
     */
    public function setAbreviation($abreviation)
    {
        $this->abreviation = $abreviation;

        return $this;
    }

    /**
     * Get abreviation
     *
     * @return string 
     */
    public function getAbreviation()
    {
        return $this->abreviation;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attribution = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add attribution
     *
     * @param \AppBundle\Entity\Attribution $attribution
     * @return Fonction
     */
    public function addAttribution(\AppBundle\Entity\Attribution $attribution)
    {
        $this->attributions[] = $attribution;
		$attribution->setFonction($this);
        return $this;
    }

    /**
     * Remove attribution
     *
     * @param \AppBundle\Entity\Attribution $attribution
     */
    public function removeAttribution(\AppBundle\Entity\Attribution $attribution)
    {
        $this->attributions->removeElement($attribution);
    }

    /**
     * Get attribution
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttributions()
    {
        return $this->attributions;
    }
}
