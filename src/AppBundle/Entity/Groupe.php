<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Model;

/**
 * Groupe
 *
 * @ORM\Table(name="app_groupes")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\GroupeRepository")
 */
class Groupe
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
     *
     *
     * @var Groupe
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Groupe", inversedBy="enfants", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     *
     * @var ArrayCollection 
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Groupe", mappedBy="parent", cascade={"persist"})
     */
    private $enfants;

	/**      
	 * @var ArrayCollection 
     * 
     * @ORM\OneToMany(targetEntity="Attribution", mappedBy="groupe", cascade={"persist"})
     */
    private $attributions;

    /**
     * @var Model $model
     * 
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Model", inversedBy="groupes", cascade={"persist"})
     */
    private $model;

    /**
     * Determine si le groupe est ouverte ou pas. On ne pourra pas lui ajouter des attibution si
     * il est fermé.
     *
     * @var Boolean $active
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * Constructor
     */
    public function __construct($nom = "")
    {
        $this->enfants = new \Doctrine\Common\Collections\ArrayCollection();

        $this->nom = $nom;
        $this->active = true;
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
     * @return Groupe
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
        return ucwords($this->nom);
    }

    /**
     * Set parent
     *
     * @param \stdClass $parent
     * @return Groupe
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \stdClass 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get all parents recursively
     * @return array
     */
    public function getParentsRecursive() {

        /** @var Geniteur $parents */
        $parents = array(0 => $this);
        $i       = 0;

        while($parents[$i]->getParent() != null){

            $parents[$i + 1] = $parents[$i]->getParent();
            $i++;
        }

        return array_reverse($parents);
    }

    /**
     * Set enfants
     *
     * @param array $enfants
     * @return Groupe
     */
    public function setEnfants($enfants)
    {
        $this->enfants = $enfants;
        foreach($enfants as $enfant)
            $enfant->setParent($this);
    
        return $this;
    }



    /**
     * Get enfants
     *
     * @return ArrayCollection
     */
    public function getEnfants()
    {
        return $this->enfants;
    }

    public function getEnfantsRecursive($main = false) {

        $enfants = $this->getEnfants()->toArray();

        foreach($enfants as $g)
            $enfants = array_merge($enfants, $g->getEnfantsRecursive());

        if($main) $enfants[] = $this;

        return $enfants;
    }
    
    /**
     * Add enfants
     *
     * @param \AppBundle\Entity\Groupe $enfants
     * @return Groupe
     */
    public function addEnfant(\AppBundle\Entity\Groupe $enfants)
    {
        $this->enfants[] = $enfants;
    
        return $this;
    }

    /**
     * Remove enfants
     *
     * @param \AppBundle\Entity\Groupe $enfants
     */
    public function removeEnfant(\AppBundle\Entity\Groupe $enfants)
    {
        $this->enfants->removeElement($enfants);
    }

    /**
     * Add attributions
     *
     * @param \AppBundle\Entity\Attribution $attributions
     * @return Groupe
     */
    public function addAttribution(\AppBundle\Entity\Attribution $attributions)
    {
        $this->attributions[] = $attributions;
    
        return $this;
    }

    /**
     * Remove attributions
     *
     * @param \AppBundle\Entity\Attribution $attributions
     */
    public function removeAttribution(\AppBundle\Entity\Attribution $attributions)
    {
        $this->attributions->removeElement($attributions);
    }

    /**
     * Get attributions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttributions()
    {
        return $this->attributions;
    }

    /**
     * Set model
     *
     * @param Model $model
     * @return Groupe
     */
    public function setModel(Model $model = null)
    {
        $this->model = $model;
        $model->addGroupe($this);
        return $this;
    }

    /**
     * Get model
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    public function getMembers()
    {

        $members = array();
        $today   = new \Datetime();

        if($this->getAttributions() != null)
        {
            foreach ($this->getAttributions() as $attribution) {
                if ($attribution->getDateFin() == null || $attribution->getDateFin() >= $today)
                    array_push($members, $attribution->getMembre());

            }
        }


        return $members;

    }

    public function getMembersRecursive()
    {

        $members = $this->getMembers();

        foreach ($this->getEnfants() as $childGroup) {
            $members = array_merge($members, $childGroup->getMembersRecursive());
        }

        return $members;
    }


    /**
     * Retourne le chef du groupe si il y en a un. Se base sur la fonction liée au type du groupe
     * @return Membre le chef du groupe
     */
    public function getChef() {

        foreach($this->getMembers() as $m) {

            if($m->getActiveAttribution()->getFonction() == $this->getGroupeReference()->getFonctionChef())
                return $m;
        }

        return null; //renvoie null sinon
    }


    /**
     * Set active
     *
     * @param boolean $active
     * @return Groupe
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Is active
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return String
     */
    public function __toString()
    {
        return ucfirst($this->getNom());
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }
}
