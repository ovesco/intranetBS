<?php

namespace Interne\ActiviteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Activite
 *
 * @ORM\Table(name="activite_activite")
 * @ORM\Entity
 */
class Activite
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
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebut", type="datetime")
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFin", type="datetime")
     */
    private $dateFin;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Membre")
     * @ORM\JoinTable(name="activites_organisateurs",
     *      joinColumns={@ORM\JoinColumn(name="activite_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="membre_id", referencedColumnName="id")}
     *      )
     */
    private $organisateurs;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Membre")
     * @ORM\JoinTable(name="activites_participants",
     *      joinColumns={@ORM\JoinColumn(name="activite_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="membre_id", referencedColumnName="id")}
     *      )
     */
    private $participants;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Groupe")
     * @ORM\JoinTable(name="activites_groupes",
     *      joinColumns={@ORM\JoinColumn(name="activite_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="groupe_id", referencedColumnName="id")}
     *      )
     */
    private $groupes;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Interne\ActiviteBundle\Entity\Depense", mappedBy="activite", cascade={"persist", "remove"})
     */
    private $depenses;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->organisateurs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->participants = new \Doctrine\Common\Collections\ArrayCollection();
        $this->depenses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groupes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Activite
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
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return Activite
     */
    public function setDateDebut($dateDebut)
    {
        if(is_string($dateDebut)) $dateDebut = new \Datetime($dateDebut);
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
     * @param \DateTime $dateFin
     * @return Activite
     */
    public function setDateFin($dateFin)
    {
        if(is_string($dateFin)) $dateFin = new \Datetime($dateFin);
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
     * Add organisateurs
     *
     * @param \AppBundle\Entity\Membre $organisateurs
     * @return Activite
     */
    public function addOrganisateur(\AppBundle\Entity\Membre $organisateurs)
    {
        $this->organisateurs[] = $organisateurs;

        return $this;
    }

    /**
     * Remove organisateurs
     *
     * @param \AppBundle\Entity\Membre $organisateurs
     */
    public function removeOrganisateur(\AppBundle\Entity\Membre $organisateurs)
    {
        $this->organisateurs->removeElement($organisateurs);
    }

    /**
     * Vérifie si l'activité possède un organisateur
     * @param \AppBundle\Entity\Membre $organisateurs
     * @return boolean
     */
    public function hasOrganisateur(\AppBundle\Entity\Membre $organisateur) {

        return $this->organisateurs->contains($organisateur);
    }

    /**
     * Get organisateurs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrganisateurs()
    {
        return $this->organisateurs;
    }

    /**
     * Add participants
     *
     * @param \AppBundle\Entity\Membre $participants
     * @return Activite
     */
    public function addParticipant(\AppBundle\Entity\Membre $participants)
    {
        $this->participants[] = $participants;

        return $this;
    }

    /**
     * Remove participants
     *
     * @param \AppBundle\Entity\Membre $participants
     */
    public function removeParticipant(\AppBundle\Entity\Membre $participants)
    {
        $this->participants->removeElement($participants);
    }

    /**
     * Get participants
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * Add depenses
     *
     * @param \Interne\ActiviteBundle\Entity\Depense $depenses
     * @return Activite
     */
    public function addDepense(\Interne\ActiviteBundle\Entity\Depense $depenses)
    {
        $this->depenses[] = $depenses;
        $depenses->setActivite($this);
        return $this;
    }

    /**
     * Remove depenses
     *
     * @param \Interne\ActiviteBundle\Entity\Depense $depenses
     */
    public function removeDepense(\Interne\ActiviteBundle\Entity\Depense $depenses)
    {
        $this->depenses->removeElement($depenses);
    }

    /**
     * Get depenses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDepenses()
    {
        return $this->depenses;
    }

    /**
     * Add groupes
     *
     * @param \AppBundle\Entity\Groupe $groupes
     * @return Activite
     */
    public function addGroupe(\AppBundle\Entity\Groupe $groupes)
    {
        $this->groupes[] = $groupes;

        return $this;
    }

    /**
     * Remove groupes
     *
     * @param \AppBundle\Entity\Groupe $groupes
     */
    public function removeGroupe(\AppBundle\Entity\Groupe $groupes)
    {
        $this->groupes->removeElement($groupes);
    }

    /**
     * Get groupes
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getGroupes()
    {
        return $this->groupes;
    }

    public function hasGroupe(\AppBundle\Entity\Groupe $groupe) {

        return ($this->groupes == null) ? false : $this->groupes->contains($groupe);
    }
}
