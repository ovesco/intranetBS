<?php

namespace Interne\SecurityBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="security_users")
 * @ORM\Entity(repositoryClass="Interne\SecurityBundle\Entity\UserRepository")
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
     * @ORM\OneToOne(targetEntity="Interne\FichierBundle\Entity\Membre")
     */
    private $membre;

	 /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    /**
     * @ORM\ManyToMany(targetEntity="Interne\SecurityBundle\Entity\Role", inversedBy="users")
     */
    private $roles;
    
    

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

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
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
     * Retourne les roles de l'utilisateur
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }
    
    /**
     * Add roles
     *
     * @param \Interne\SecurityBundle\Entity\Role roles
     * @return User
     */
    public function addRole(\Interne\SecurityBundle\Entity\Role $roles) {
    	
    	$this->roles[] = $roles;
    	return $this;
    }
    
    /**
     * Remove roles
     *
     * @param \Interne\SecurityBundle\Entity\Role roles
     */
    public function removeUser(\Interne\SecurityBundle\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
    }
   
    /**
     * Get membre
     *
     * @return \Interne\FichierBundle\Entity\Membre 
     */
    public function getMembre()
    {
        return $this->membre;
    }
}
