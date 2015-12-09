<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Categorie
 *
 * @ORM\Table(name="app_categorie")
 * @Gedmo\Loggable
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
     * @Gedmo\Versioned
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="Model", mappedBy="categories")
     * @ORM\JoinTable(name="app_categorie_model",
     *      joinColumns={@ORM\JoinColumn(name="categorie_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="model_id", referencedColumnName="id")}
     *      )
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
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Categorie
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
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
     * Set description
     *
     * @param string $description
     * @return Categorie
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
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
        if(!$model->getCategories()->contains($this)){
            $model->addCategorie($this);
        }

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
        if($model->getCategories()->contains($this)) {
            $model->removeCategorie($this);
        }
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
        /** @var Model $model */
        foreach($this->models as $model)
        {
            $this->removeModel($model);
        }
        /** @var Model $model */
        foreach($models as $model)
        {
            $this->addModel($model);
        }
    }

    /**
     * Possible de supprimer uniquement si aucun model liés.
     * @return bool
     */
    public function isRemovable()
    {
        return $this->models->isEmpty();
    }
}
