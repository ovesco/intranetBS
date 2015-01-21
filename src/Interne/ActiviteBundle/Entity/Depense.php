<?php

namespace Interne\ActiviteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Interne\ActiviteBundle\Utils\Tableur\Encoder;

/**
 * Depense
 *
 * @ORM\Table(name="activite_depense")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Depense
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
     * @var Membre
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Membre")
     */
    private $createur;

    /**
     * @var \Datetime
     *
     * @ORM\Column(name="dateCreation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \Datetime
     *
     * @ORM\Column(name="derniereModification", type="datetime")
     */
    private $derniereModification;

    /**
     * @var decimal
     *
     * @ORM\Column(name="total", type="decimal", precision=20, scale=2)
     */
    private $total;

    /**
     * @var string
     *
     * @ORM\Column(name="categorie", type="string", length=255)
     */
    private $categorie;

    /**
     * @var Activite
     *
     * @ORM\ManyToOne(targetEntity="Interne\ActiviteBundle\Entity\Activite", inversedBy="depenses")
     */
    private $activite;

    /**
     * @var text
     *
     * @ORM\Column(name="tableur", type="text")
     */
    private $tableur;


    public function __construct() {

        $this->dateCreation = new \Datetime("now");
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
     * @return Depense
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
     * Set categorie
     *
     * @param string $categorie
     * @return Depense
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return string 
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set activite
     *
     * @param \Interne\ActiviteBundle\Entity\Activite $activite
     * @return Depense
     */
    public function setActivite(\Interne\ActiviteBundle\Entity\Activite $activite = null)
    {
        $this->activite = $activite;

        return $this;
    }

    /**
     * Get activite
     *
     * @return \Interne\ActiviteBundle\Entity\Activite 
     */
    public function getActivite()
    {
        return $this->activite;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Depense
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime 
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set createur
     *
     * @param \AppBundle\Entity\Membre $createur
     * @return Depense
     */
    public function setCreateur(\AppBundle\Entity\Membre $createur = null)
    {
        $this->createur = $createur;

        return $this;
    }

    /**
     * Get createur
     *
     * @return \AppBundle\Entity\Membre 
     */
    public function getCreateur()
    {
        return $this->createur;
    }

    /**
     * Set derniereModification
     *
     * @param \DateTime $derniereModification
     * @return Depense
     */
    public function setDerniereModification($derniereModification)
    {
        $this->derniereModification = $derniereModification;

        return $this;
    }

    /**
     * Get derniereModification
     *
     * @return \DateTime 
     */
    public function getDerniereModification()
    {
        return $this->derniereModification;
    }

    /**
     * Set tableur
     *
     * @param string $tableur
     * @return Depense
     */
    public function setTableur($tableur)
    {
        $this->tableur = $tableur;

        return $this;
    }

    /**
     * Get tableur
     *
     * @return string 
     */
    public function getTableur()
    {
        return $this->tableur;
    }

    /**
     * Après être chargée, on formatte le tableur
     * @ORM\PostLoad
     * @ORM\PostUpdate
     * @ORM\PostPersist
     */
    function postStuff() {

        $encoder = new Encoder();

        if($this->tableur == null)
            $this->tableur = serialize(array(array(),array(),array(),array()));
        $this->tableur = $encoder::decode($this->tableur);
    }

    /**
     * Juste avant d'être persistée, on encode le tableur, et on change la date de dernière
     * modification
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    function preStuff() {

        $encoder = new Encoder();

        $this->tableur = $encoder::encode($this->tableur);
        $this->derniereModification = new \Datetime();
    }

    /**
     * Set total
     *
     * @param string $total
     * @return Depense
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return string 
     */
    public function getTotal()
    {
        return $this->total;
    }
}
