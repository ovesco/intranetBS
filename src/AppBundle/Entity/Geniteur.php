<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Personne;

/**
 * Geniteur
 *
 * @ORM\Table(name="app_geniteurs")
 * @ORM\Entity
 */
class Geniteur extends Personne
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
    private $nom;
    
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
     * @return string
     */
    public function getNom()
    {
        if($this->nom == null)
        {

            //TODO one to one avec famille pour récupérer le nom
            return null;
        }
        else
            return ucwords($this->nom);
    }
}
