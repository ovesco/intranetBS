<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Type;

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
     * @var Groupe
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Groupe", inversedBy="enfants")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @var ArrayCollection 
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Groupe", mappedBy="parent")
     */
    private $enfants;

	/**      
	 * @var ArrayCollection 
     * 
     * @ORM\OneToMany(targetEntity="Attribution", mappedBy="groupe", cascade={"persist"})
     */
    private $attributions;

    /**
     * @var Type $type
     * 
     * @ORM\ManyToOne(targetEntity="Type", inversedBy="groupes", cascade={"persist"})
     */
    private $type;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->enfants = new \Doctrine\Common\Collections\ArrayCollection();
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
    
        return $this;
    }

    /**
     * Get enfants
     *
     * @return array 
     */
    public function getEnfants()
    {
        return $this->enfants;
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
     * Set type
     *
     * @param \AppBundle\Entity\Type $type
     * @return Groupe
     */
    public function setType(\AppBundle\Entity\Type $type = null)
    {
        $this->type = $type;
	    $type->addGroupe($this);
        return $this;
    }

    /**
     * Get type
     *
     * @return \AppBundle\Entity\Type 
     */
    public function getType()
    {
        return $this->type;
    }

    public function getMembers()
    {

        $members = array();
        $today   = new \Datetime();

        foreach ($this->getAttributions() as $attribution) {
            if ($attribution->getDateFin() == null || $attribution->getDateFin() > $today)
                array_push($members, $attribution->getMembre());

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
     * Renvoie un array contenant les membres qui composent l'EM d'une troupe ou d'une meute. Si cette méthode est appelée
     * depuis ailleurs qu'une troupe ou une meute, elle soulèvera une exception
     * @throws \Exception si groupe n'est ni une troupe ni une meute
     * @return array l'em de l'unité
     */
    public function getEm() {

        if(strtolower($this->getType()->getNom()) != 'meute' && strtolower($this->getType()->getNom()) != 'troupe')
            throw new \Exception($this->getNom() . " n'est pas une troupe !");

        $em = array('cu' => null, 'cua' => null, 'adjoints' => array(), 'c' => array());

        foreach($this->getMembersRecursive() as $k => $m) {

            if(
                strtolower($m->getActiveAttribution()->getFonction()->getAbreviation()) == 'ct' ||
                strtolower($m->getActiveAttribution()->getFonction()->getAbreviation()) == 'cm'
            ) {
                $em['cu'] = $m;
            }

            else if(
                strtolower($m->getActiveAttribution()->getFonction()->getAbreviation()) == 'cta' ||
                strtolower($m->getActiveAttribution()->getFonction()->getAbreviation()) == 'cma'
            ) {
                $em['cua'] = $m;
            }

            else if(strtolower($m->getActiveAttribution()->getFonction()->getAbreviation()) == 'adj') {

                $em['adjoints'][] = $m;
            }

            else if(
                strtolower($m->getActiveAttribution()->getFonction()->getAbreviation()) == 'cp' ||
                strtolower($m->getActiveAttribution()->getFonction()->getAbreviation()) == 'cs'
            ) {
                $em['c'][] = $m;
            }
        }

        return $em;
    }

    /**
     * Retourne le chef du groupe si il y en a un. Se base sur la fonction liée au type du groupe
     * @return Membre le chef du groupe
     */
    public function getChef() {

        foreach($this->getMembers() as $m) {

            if($m->getActiveAttribution()->getFonction() == $this->getType()->getFonctionChef())
                return $m;
        }

        return null; //renvoie null sinon
    }
}
