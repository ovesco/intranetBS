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


    /*
     * Cette variable sert à désactiver une adresse lorsqu'on a des retours parce que
     * l'adresse n'est plus valide.
     */
    /**
     *
     * @var boolean $validity
     *
     * @ORM\Column(name="validity", type="boolean")
     * @Assert\NotBlank()
     */
    private $validity;

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
     * @var boolean
     * @ORM\Column(name="adressable", type="boolean")
     */
    private $adressable;
    
    /**
     * @var text
     * 
     * @ORM\Column(name="remarques", type="text", nullable=true)
     */
     private $remarques;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=255, nullable=true)
     */
    private $telephone;

    /*
     * La méthode d'envoi determine si la personne va recevoir
     * sont courrier par email ou par courrier.
     */

    /**
     * @var methodeEnvoi
     *
     * @ORM\Column(name="methode_envoi", type="string", columnDefinition="ENUM('Email', 'Courrier')")
     */
    private $methodeEnvoi;

    /**
     * Is Receivable
     *
     * @return bool
     */
    public function isReceivable()
    {
        return $this->adressable && $this->validity;
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
     * Set adressable
     *
     * @param boolean $adressable
     * @return Adresse
     */
    public function setAdressable($adressable)
    {
        $this->adressable = $adressable;
    
        return $this;
    }

    /**
     * Get adressable
     *
     * @return boolean 
     */
    public function getAdressable()
    {
        return $this->adressable;
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
     * Set methodeEnvoi
     *
     * @param string $methodeEnvoi
     * @return Adresse
     */
    public function setMethodeEnvoi($methodeEnvoi)
    {
        $this->methodeEnvoi = $methodeEnvoi;

        return $this;
    }

    /**
     * Get methodeEnvoi
     *
     * @return string
     */
    public function getMethodeEnvoi()
    {
        return $this->methodeEnvoi;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Adresse
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

    /**
     * Set validity
     *
     * @param boolean $validity
     * @return Adresse
     */
    public function setValidity($validity)
    {
        $this->validity = $validity;

        return $this;
    }

    /**
     * Get validity
     *
     * @return boolean
     */
    public function getValidity()
    {
        return $this->validity;
    }


}
