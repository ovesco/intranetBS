<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Geniteur
 *
 * @ORM\Entity
 * @ORM\Table(name="app_geniteurs")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"pere" = "Pere", "mere" = "Mere"})
 */
abstract class Geniteur extends Personne
{

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    protected $nom;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="profession", type="string", nullable=true)
     */
    private $profession;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Famille", inversedBy="geniteur")
     * @ORM\JoinColumn(name="famille_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $famille;

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
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        if ($this->nom !== null)
            return ucwords($this->nom);
        elseif ($this->getFamille() !== null)
            return $this->getFamille()->getNom();
        else
            return 'Inconnu';
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
     * Get famille
     *
     * @return Famille
     */
    public function getFamille()
    {
        return $this->famille;
    }

    /**
     * Set famille
     *
     * @param Famille $famille
     * @return Pere
     */
    public function setFamille($famille)
    {
        $this->famille = $famille;

        return $this;
    }

}
