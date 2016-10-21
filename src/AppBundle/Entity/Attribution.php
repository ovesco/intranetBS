<?php

namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Attribution
 *
 * @ORM\Table(name="app_attributions")
 * @Gedmo\Loggable
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Attribution
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
     * @ORM\Column(name="dateDebut", type="date")
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="dateFin", type="date", nullable=true)
     */
    private $dateFin;
    
    /**
     * @var Groupe $groupe
     *
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="Groupe", inversedBy="attributions")
     * @ORM\JoinColumn(name="groupe_id", referencedColumnName="id")
     */
     private $groupe;
     
    /**
     * @var Membre $membre
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Membre", inversedBy="attributions")
     * @ORM\JoinColumn(name="membre_id", referencedColumnName="id", onDelete="CASCADE")
     */
     private $membre;
     
    /**
     * @var Fonction $fonction
     *
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Fonction", inversedBy="attributions")
     * @ORM\JoinColumn(name="fonction_id", referencedColumnName="id")
     */
     private $fonction;

    /**
     * @var String
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="remarques", type="text", nullable=true)
     */
    private $remarques;


    public function __construct()
    {
        $this->dateDebut = new \DateTime();
    }

    public function __tostring()
    {
        return $this->getFonction() . ' à ' . $this->getGroupe();
    }

    /**
     * Get fonction
     *
     * @return \AppBundle\Entity\Fonction
     */
    public function getFonction()
    {
        return $this->fonction;
    }

    /**
     * Set fonction
     *
     * @param \AppBundle\Entity\Fonction $fonction
     * @return Attribution
     */
    public function setFonction(\AppBundle\Entity\Fonction $fonction = null)
    {
        $this->fonction = $fonction;

        return $this;
    }

    /**
     * Get groupe
     *
     * @return \AppBundle\Entity\Groupe
     */
    public function getGroupe()
    {
        return $this->groupe;
    }

    /**
     * Set groupe
     *
     * @param \AppBundle\Entity\Groupe $groupe
     * @return Attribution
     */
    public function setGroupe(\AppBundle\Entity\Groupe $groupe = null)
    {
        $this->groupe = $groupe;

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
     * Get dateDebut
     *
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return Attribution
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set dateFin
     *
     * @param string $dateFin
     * @return Attribution
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

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
     * @param Membre $membre
     * @return Attribution
     */
    public function setMembre(Membre $membre)
    {
        $this->membre = $membre;
        return $this;
    }

    /**
     * @return String
     */
    public function getRemarques()
    {
        return $this->remarques;
    }

    /**
     * @param String $remarques
     */
    public function setRemarques($remarques)
    {
        $this->remarques = $remarques;
    }

}
