<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * ObtentionDistinction
 *
 * @ORM\Table(name="app_obtention_distinctions")
 * @Gedmo\Loggable
 * @ORM\Entity
 *
 *
 * todo NUR ajouter un champ remarque
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
     * @Gedmo\Versioned
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var Distinction $distinctions
     *
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Distinction", inversedBy="obtentionDistinctions")
     */
    private $distinction;


    /**
     * @var membre
     *
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Membre", inversedBy="distinctions")
     * @ORM\JoinColumn(name="membre_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $membre;


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getDistinction()->getNom(); // TODO : peut mieux faire
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

    /**
     * Set distinction
     *
     * @param \AppBundle\Entity\Distinction $distinction
     * @return ObtentionDistinction
     */
    public function setDistinction(Distinction $distinction = null)
    {
        $this->distinction = $distinction;

        return $this;
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
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return ObtentionDistinction
     */
    public function setDate($date)
    {
        $this->date = $date;

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
     * Set membre
     *
     * @param \AppBundle\Entity\Membre $membre
     * @return ObtentionDistinction
     */
    public function setMembre(Membre $membre = null)
    {
        $this->membre = $membre;

        return $this;
    }
}
