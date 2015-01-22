<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Adresse
 *
 * @ORM\Table(name="app_adresses")
 * @ORM\Entity
 */
class Adresse
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
     * @var boolean
     * @ORM\Column(name="expediable", type="boolean")
     */
    private $expediable;

    /**
     * @var string
     *
     * @ORM\Column(name="rue", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min = "3")
     */
    private $rue;

    /**
     * @var integer
     *
     * @ORM\Column(name="npa", type="integer")
     * @Assert\NotBlank()
     * @Assert\Length(min = "3")
     */
    private $npa;

    /**
     * @var string
     *
     * @ORM\Column(name="localite", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min = "3")
     */
    private $localite;


    
    /**
     * @var text
     * 
     * @ORM\Column(name="remarques", type="text", nullable=true)
     */
     private $remarques;

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
     * Set rue
     *
     * @param string $rue
     * @return Adresse
     */
    public function setRue($rue)
    {
        $this->rue = $rue;
    
        return $this;
    }

    /**
     * Get rue
     *
     * @return string 
     */
    public function getRue()
    {
        return $this->rue;
    }

    /**
     * Set npa
     *
     * @param integer $npa
     * @return Adresse
     */
    public function setNpa($npa)
    {
        $this->npa = $npa;
    
        return $this;
    }

    /**
     * Get npa
     *
     * @return integer 
     */
    public function getNpa()
    {
        return $this->npa;
    }

    /**
     * Set localite
     *
     * @param string $localite
     * @return Adresse
     */
    public function setLocalite($localite)
    {
        $this->localite = $localite;
    
        return $this;
    }

    /**
     * Get localite
     *
     * @return string 
     */
    public function getLocalite()
    {
        return $this->localite;
    }

    /**
     * is expediable
     *
     * @return bool
     */
    public function isExperdiable()
    {
        return $this->expediable;
    }

    /**
     * Set expediable
     *
     * @param boolean $expediable
     * @return Adresse
     */
    public function setExpediable($expediable)
    {
        $this->expediable = $expediable;
    
        return $this;
    }

    /**
     * Get expediable
     *
     * @return boolean 
     */
    public function getExpediable()
    {
        return $this->expediable;
    }

    /**
     * Set remarques
     *
     * @param string $remarques
     * @return Adresse
     */
    public function setRemarques($remarques)
    {
        $this->remarques = $remarques;
    
        return $this;
    }

    /**
     * Get remarques
     *
     * @return string 
     */
    public function getRemarques()
    {
        return $this->remarques;
    }

    public function __toString() {
        return $this->rue.'\n'.$this->npa.' '.$this->localite;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return Adresse
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



}
