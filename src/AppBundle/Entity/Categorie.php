<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Categorie
 *
 * @ORM\Table(name="app_categorie")
 * @ORM\Entity
 */
class Categorie
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
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;


    /*
    /**
     * @ORM\ManyToMany(targetEntity="GroupeModel")
     * @ORM\JoinTable(name="app_groupe_type_groupe_model",
     *      joinColumns={@ORM\JoinColumn(name="groupe_type_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="groupe_model_id", referencedColumnName="id")}
     *      )
     *
     *
     *
    private $groupeModels;

    */

    /**
     * Constructor
     */
    public function __construct()
    {
        //$this->$groupeModels = new ArrayCollection();
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
     * Set nom
     *
     * @param string $nom
     * @return GroupeType
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
     * Set description
     *
     * @param string $description
     * @return GroupeType
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set groupeModels
     *
     * @param string $groupeModels
     * @return GroupeType
     */
    public function setGroupeModels($groupeModels)
    {
        $this->groupeModels = $groupeModels;

        return $this;
    }

    /**
     * Get groupeModels
     *
     * @return string 
     */
    public function getGroupeModels()
    {
        return $this->groupeModels;
    }
}
