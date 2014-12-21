<?php

namespace Interne\SecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Validator
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Interne\SecurityBundle\Entity\ValidatorRepository")
 */
class Validator
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
     * @var \stdClass
     *
     * @ORM\Column(name="avant", type="object")
     */
    private $avant;

    /**
     * @var \stdClass
     *
     * @ORM\Column(name="apres", type="object")
     */
    private $apres;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var \stdClass
     *
     * @ORM\Column(name="auteur", type="object")
     */
    private $auteur;


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
     * Set avant
     *
     * @param \stdClass $avant
     * @return Validator
     */
    public function setAvant($avant)
    {
        $this->avant = $avant;

        return $this;
    }

    /**
     * Get avant
     *
     * @return \stdClass 
     */
    public function getAvant()
    {
        return $this->avant;
    }

    /**
     * Set apres
     *
     * @param \stdClass $apres
     * @return Validator
     */
    public function setApres($apres)
    {
        $this->apres = $apres;

        return $this;
    }

    /**
     * Get apres
     *
     * @return \stdClass 
     */
    public function getApres()
    {
        return $this->apres;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Validator
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set auteur
     *
     * @param \stdClass $auteur
     * @return Validator
     */
    public function setAuteur($auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return \stdClass 
     */
    public function getAuteur()
    {
        return $this->auteur;
    }
}
