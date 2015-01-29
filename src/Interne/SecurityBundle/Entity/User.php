<?php

namespace Interne\SecurityBundle\Entity;

use Interne\SecurityBundle\Utils\RolesUtil;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\ManyToMany(targetEntity="Interne\SecurityBundle\Entity\Role", inversedBy="users")
     */
    private $roles;

    /**
     * @var string
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;
    
    

    public function __construct()
    {
        $this->isActive = true;
        $this->roles 	= new ArrayCollection();
    }

    public function setUsername($username){
        $this->username = $username;
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
        return $this->salt;
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
    public function removeRole(\Interne\SecurityBundle\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
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
     * On fournit au User les roles qu'il possède au travers de ses attributions
     * @ORM\PostLoad
     */
    public function loadRoles()
    {
        $rolesUtil = new RolesUtil();
        $roles     = array();

        $attributions = $this->getMembre()->getActiveAttributions();

        foreach($attributions as $a) {
            foreach ($a->getFonction()->getRoles() as $r)
                $roles = array_merge($roles, $r->getEnfantsRecursive(true));
        }

        $roles = $rolesUtil->removeDoublons($roles);

        foreach($roles as $r)
            $this->roles->add($r);
    }
}
