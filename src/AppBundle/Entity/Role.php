<?php

namespace AppBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Role
 * @ORM\Table(name="app_roles")
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
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
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
     * @ORM\OneToMany(targetEntity="Role", mappedBy="parent", cascade={"persist"}, fetch="EAGER")
     */
    private $childs;

    /**
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="childs", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_role_id", referencedColumnName="id", nullable=true)
     */
    private $parent;



    public function __construct()
    {
        $this->childs = new ArrayCollection();
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
     * Set parent
     *
     * @param \AppBundle\Entity\Role $parent
     * @return Role
     */
    public function setParent(\AppBundle\Entity\Role $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\Role 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add child
     *
     * @param \AppBundle\Entity\Role $child
     * @return Role
     */
    public function addChild(\AppBundle\Entity\Role $child)
    {
        $this->childs[] = $child;
        $child->setParent($this);
        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\Role $child
     */
    public function removeChild(\AppBundle\Entity\Role $child)
    {
        $this->childs->removeElement($child);
    }

    /**
     * Get childs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * Cette fonction permet de retourner tout les enfant du role en fonction
     * de la hierarchie des roles
     *
     * @param bool $selfInclude
     * @return array
     */
    public function getChildsRecursive($selfInclude = false) {

        $childs = $this->getChilds()->toArray();

        /** @var Role $role */
        foreach($childs as $role) {
            $childs = array_merge($childs, $role->getChildsRecursive());
        }

        if($selfInclude) $childs[] = $this;

        return $childs;
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
