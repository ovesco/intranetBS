<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Geniteur
 *
 * @ORM\Table(name="app_geniteurs")
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"pere" = "Pere", "mere" = "Mere"})
 */
abstract class Geniteur extends Personne
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
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(min = "2")
     */
    protected $nom;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="profession", type="string", nullable=true)
     */
    private $profession;

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
     * Set profession
     *
     * @param integer $profession
     * @return Geniteur
     */
    public function setProfession($profession)
    {
        $this->profession = $profession;
    
        return $this;
    }

    /**
     * Get profession
     *
     * @return integer 
     */
    public function getProfession()
    {
        return $this->profession;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Famille
     */
    public function setNom($nom)
    {
        $this->nom = ucwords($nom);

        return $this;
    }

    /**
     * Get nom
     *
     * On a besoin du lien avec la famille pour connaitre le nom du Geniteur.
     * Donc se fait dans les class filles.
     *
     * @return string
     */
    abstract public function getNom();


}
