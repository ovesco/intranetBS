<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Distinction
 *
 * @ORM\Table(name="app_distinctions")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DistinctionRepository")
 */
class Distinction
{

    use RemarquableTrait;

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
     * @Gedmo\Versioned
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ObtentionDistinction", mappedBy="distinction")
     */
    private $obtentionDistinctions;
    



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
     * @return Distinction
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
    public function __construct($name = "")
    {
        $this->obtentionDistinctions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setNom($name);
    }

    public function __toString()
    {
        return $this->getNom();
    }

    /**
     * Add obtentionDistinctions
     *
     * @param \AppBundle\Entity\ObtentionDistinction $obtentionDistinctions
     * @return Distinction
     */
    public function addObtentionDistinction(\AppBundle\Entity\ObtentionDistinction $obtentionDistinctions)
    {
        $this->obtentionDistinctions[] = $obtentionDistinctions;

        return $this;
    }

    /**
     * Remove obtentionDistinctions
     *
     * @param \AppBundle\Entity\ObtentionDistinction $obtentionDistinctions
     */
    public function removeObtentionDistinction(\AppBundle\Entity\ObtentionDistinction $obtentionDistinctions)
    {
        $this->obtentionDistinctions->removeElement($obtentionDistinctions);
    }

    /**
     * Get obtentionDistinctions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getObtentionDistinctions()
    {
        return $this->obtentionDistinctions;
    }

    /**
     * @return bool
     */
    public function isRemovable()
    {
        return $this->obtentionDistinctions->isEmpty();
    }

}
