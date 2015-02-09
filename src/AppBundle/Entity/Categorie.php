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



    /**
     * @ORM\ManyToMany(targetEntity="Model", mappedBy="categories")
     * @ORM\JoinTable(name="app_categorie_model",
     *      joinColumns={@ORM\JoinColumn(name="categorie_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="model_id", referencedColumnName="id")}
     *      )
     *
     *
     */
    private $models;



    /**
     * Constructor
     */
    public function __construct($nom = null)
    {
        $this->nom = $nom;
        $this->models = new ArrayCollection();
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
     * Add model
     *
     * @param Model $model
     * @return Categorie
     */
    public function addModel(Model $model)
    {
        $this->models[] = $model;

        return $this;
    }

    /**
     * Remove model
     *
     * @param Model $model
     */
    public function removeModel(Model $model)
    {
        $this->models->removeElement($model);
    }

    /**
     * Get models
     *
     * @return ArrayCollection
     */
    public function getModels()
    {
        return $this->models;
    }

    /**
     * Set models
     *
     * @param $models
     */
    public function setModels($models)
    {
        $this->models = $models;
    }
}
