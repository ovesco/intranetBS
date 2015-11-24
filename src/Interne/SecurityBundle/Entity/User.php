<?php

namespace Interne\SecurityBundle\Entity;

use AppBundle\Entity\Attribution;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="security_users")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface, \Serializable
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
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Membre")
     */
    private $membre;

	 /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    /**
     * @ORM\ManyToMany(targetEntity="Interne\SecurityBundle\Entity\Role")
     * @ORM\JoinTable(name="roles_users",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     */
    private $roles;

    /**
     * @var \Datetime
     *
     * @ORM\Column(name="last_connexion", type="datetime")
     */
    private $lastConnexion;
    

    public function __construct()
    {
        $this->isActive = true;
        $this->salt 	= md5(uniqid(null, true));
        $this->role 	= new ArrayCollection();
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set a new password already encoded
     * @param string $password
     * @return User
     */
    public function setPassword($password) {

        $this->password = $password;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }
    
    /**
     * Vérifie que l'utilisateur possède un role donné
     * @param string $role
     * @return boolean
     */
    public function hasRole($role) {

        foreach($this->getRoles() as $r)
            if($r->getRole() == $role)
                return true;

        return false;
    }

    /**
     * Retourne les roles de l'utilisateur
     */
    public function getRoles()
    {
        $roles = $this->roles->toArray();

        /** @var Attribution $attr */
        foreach ($this->getMembre()->getActiveAttributions() as $attr) {
            foreach ($attr->getFonction()->getRoles() as $r)
                $roles[] = $r;
        }

        return $roles;
    }
    
    /**
     * Get membre
     *
     * @return \AppBundle\Entity\Membre
     */
    public function getMembre()
    {
        return $this->membre;
    }

    /**
     * Set membre
     * @param \AppBundle\Entity\Membre $membre
     * @return User
     */
    public function setMembre(\AppBundle\Entity\Membre $membre) {

        $this->membre = $membre;
        return $this;
    }

    /**
     * Add roles
     *
     * @param \Interne\SecurityBundle\Entity\Role roles
     * @return User
     */
    public function addRole(\Interne\SecurityBundle\Entity\Role $roles)
    {

        $this->roles[] = $roles;
        return $this;
    }

    /**
     * Remove roles
     *
     * @param \Interne\SecurityBundle\Entity\Role roles
     */
    public function removeRole(\Interne\SecurityBundle\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
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
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get lastConnexion
     *
     * @return \DateTime
     */
    public function getLastConnexion()
    {
        return $this->lastConnexion;
    }

    /**
     * Set lastConnexion
     *
     * @param \DateTime $lastConnexion
     * @return User
     */
    public function setLastConnexion($lastConnexion)
    {
        $this->lastConnexion = $lastConnexion;

        return $this;
    }
}
