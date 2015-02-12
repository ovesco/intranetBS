<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Attribution
 *
 * @ORM\Table(name="app_attributions")
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
     * @ORM\Column(name="dateDebut", type="date")
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFin", type="date", nullable=true)
     */
    private $dateFin;
    
    /**
     * @var Groupe $groupe
     * 
     * @ORM\ManyToOne(targetEntity="Groupe", inversedBy="attributions")
     * @ORM\JoinColumn(name="groupe_id", referencedColumnName="id")
     */
     private $groupe;
     
    /**
     * @var Membre $membre
     * 
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Membre", inversedBy="attributions")
     * @ORM\JoinColumn(name="membre_id", referencedColumnName="id")
     */
     private $membre;
     
    /**
     * @var Fonction $fonction
     * 
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Fonction")
     * @ORM\JoinColumn(name="fonction_id", referencedColumnName="id")
     */
     private $fonction;

    /**
     * @var String
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
     * Set dateDebut
     *
     * @param string $dateDebut
     * @return Attribution
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;
    
        return $this;
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
     * Get dateFin
     *
     * @return \DateTime 
     */
    public function getDateFin()
    {
        return $this->dateFin;
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
     * Get groupe
     *
     * @return \AppBundle\Entity\Groupe 
     */
    public function getGroupe()
    {
        return $this->groupe;
    }

    /**
     * Set membre
     *
     * @param \AppBundle\Entity\Membre $membre
     * @return Attribution
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
     * Get fonction
     *
     * @return \AppBundle\Entity\Fonction 
     */
    public function getFonction()
    {
        return $this->fonction;
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
