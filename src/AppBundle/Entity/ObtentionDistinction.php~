<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ObtentionDistinction
 *
 * @ORM\Table(name="app_obtention_distinctions")
 * @ORM\Entity
 */
class ObtentionDistinction
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
     * @var \DateTime
     *
     * @ORM\Column(name="obtention", type="date", nullable=false)
     */
    private $obtention;

    /**
     * @var Distinction $distinctions
     * 
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Distinction", inversedBy="obtentionDistinctions")
     */
    private $distinction;


    /**
     * @var membre
     * 
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Membre", inversedBy="distinctions")
     */
    private $membre;


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
     * Set obtention
     *
     * @param \DateTime $obtention
     * @return ObtentionDistinction
     */
    public function setObtention($obtention)
    {
        $this->obtention = $obtention;
    
        return $this;
    }

    /**
     * Get obtention
     *
     * @return \DateTime 
     */
    public function getObtention()
    {
        return $this->obtention;
    }

    /**
     * Set membre
     *
     * @param \AppBundle\Entity\Membre $membre
     * @return ObtentionDistinction
     */
    public function setMembre(\AppBundle\Entity\Membre $membre = null)
    {
        $this->membre = $membre;

        return $this;
    }

    /**
     * Get membre
     *
     * @return \AppBundle\Entity\Membre 
     */
    public function getMembre()
    {
        return $this->membre;
    }

    /**
     * Set distinction
     *
     * @param \AppBundle\Entity\Distinction $distinction
     * @return ObtentionDistinction
     */
    public function setDistinction(\AppBundle\Entity\Distinction $distinction = null)
    {
        $this->distinction = $distinction;

        return $this;
    }

    /**
     * Get distinction
     *
     * @return \AppBundle\Entity\Distinction 
     */
    public function getDistinction()
    {
        return $this->distinction;
    }
}
