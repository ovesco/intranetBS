<?php

namespace Interne\SecurityBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Role
 * @ORM\Table(name="security_roles")
 * @ORM\Entity
 */
class Role implements RoleInterface
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=255, unique=true)
     */
    private $role;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="Role", mappedBy="parent", cascade={"persist"})
     */
    private $enfants;

    /**
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="enfants", cascade={"persist"})
     */
    private $parent;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="roles")
     */
    private $users;



    public function __construct()
    {
        $this->users = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Role
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return Role
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Add users
     *
     * @param \Interne\SecurityBundle\Entity\User $users
     * @return Role
     */
    public function addUser(\Interne\SecurityBundle\Entity\User $users)
    {
        $this->users[] = $users;
        
        return $this;
    }

    /**
     * Remove users
     *
     * @param \Interne\SecurityBundle\Entity\User $users
     */
    public function removeUser(\Interne\SecurityBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set parent
     *
     * @param \Interne\SecurityBundle\Entity\Role $parent
     * @return Role
     */
    public function setParent(\Interne\SecurityBundle\Entity\Role $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Interne\SecurityBundle\Entity\Role 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add enfants
     *
     * @param \Interne\SecurityBundle\Entity\Role $enfants
     * @return Role
     */
    public function addEnfant(\Interne\SecurityBundle\Entity\Role $enfants)
    {
        $this->enfants[] = $enfants;
        $enfants->setParent($this);
        return $this;
    }

    /**
     * Remove enfants
     *
     * @param \Interne\SecurityBundle\Entity\Role $enfants
     */
    public function removeEnfant(\Interne\SecurityBundle\Entity\Role $enfants)
    {
        $this->enfants->removeElement($enfants);
    }

    /**
     * Get enfants
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEnfants()
    {
        return $this->enfants;
    }

    public function getEnfantsRecursive($main = false) {

        $enfants = $this->getEnfants()->toArray();

        foreach($enfants as $r)
            $enfants = array_merge($enfants, $r->getEnfantsRecursive());

        if($main) $enfants[] = $this;

        return $enfants;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Role
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
}
