<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * User
 *
 * @ORM\Table(name="app_users")
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields="username",message="this username already exist")
 *
 * @ExclusionPolicy("all")
 *
 *
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Expose
     */
    private $id;

    /**
     * @var string $username
     *
     * @ORM\Column(name="username", type="string", length=255, unique = true)
     *
     * @Expose
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
     *
     * @Expose
     */
    private $membre;

	 /**
      * @var boolean
      *
      * @ORM\Column(name="is_active", type="boolean")
      */
    private $isActive;

    /**
     * @var array
     *
     * @ORM\Column(name="selected_roles", type="simple_array")
     */
    private $selectedRoles;

    /**
     * @var array
     *
     * This variable is only stored in session and recomputed at each login
     *
     */
    private $roles;


    /**
     * @var \Datetime
     *
     * @ORM\Column(name="last_connexion", type="datetime", nullable=true)
     *
     * @Expose
     */
    private $lastConnexion;
    

    public function __construct()
    {
        $this->membre = null;
        $this->isActive = true;
        $this->selectedRoles 	= array();
        $this->roles = null;

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
     *
     * imposed by UserInterface
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
            $this->roles,
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
            $this->roles
        ) = unserialize($serialized);
    }
    
    /**
     * Vérifie que l'utilisateur possède un role donné
     * @param string $role
     * @return boolean
     */
    public function hasRole($role) {

        return in_array($role,$this->getRoles());
    }


    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Fonction imposée par UserInterface
     *
     * Les roles sont injecté par le service UserProvider (app.user.provider)
     *
     * Retourne tout les roles de l'utilisateur.
     *
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Cherche les role déduit du membre liée à l'utilisateur
     *
     * @return array
     */
    public function getMembreRoles()
    {
        $roles = array();

        if($this->hasMembre())
        {
            /** @var Attribution $attr */
            foreach ($this->getMembre()->getActiveAttributions() as $attr)
            {
                foreach ($attr->getFonction()->getRoles() as $r)
                     $roles[] = $r;
            }
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
     * @return bool
     */
    public function hasMembre(){
        return ($this->membre != null);
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
     * Is isActive
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->isActive;
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


    /**
     * @param $role
     * @return $this
     */
    public function addSelectedRoles($role)
    {
        if(!in_array($role,$this->selectedRoles))
        {
            $this->selectedRoles[] = $role;
        }
        return $this;
    }

    /**
     * @param $role
     * @return $this
     */
    public function removeSelectedRoles($role)
    {
        if(($key = array_search($role, $this->selectedRoles)) !== false) {
            unset($this->selectedRoles[$key]);
        }
        return $this;
    }


    /**
     * Set selectedRoles
     *
     * @param array $selectedRoles
     *
     * @return User
     */
    public function setSelectedRoles($selectedRoles)
    {
        $this->selectedRoles = $selectedRoles;

        return $this;
    }

    /**
     * Get selectedRoles
     *
     * @return array
     */
    public function getSelectedRoles()
    {
        return $this->selectedRoles;
    }

}
