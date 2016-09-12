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
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields="username",message="this username already exist")
 *
 * @ExclusionPolicy("all")
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
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Role")
     * @ORM\JoinTable(name="app_roles_users",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     *
     * Cette nomenclature spécifique est nécaissaire pour éviter des conflits avec
     * la methode getRoles imposée par le UserInterface
     */
    private $savedRoles;

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
        $this->salt 	= md5(uniqid(null, true));
        $this->savedRoles 	= new ArrayCollection();
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
            if($r->getRoles() == $role)
                return true;

        return false;
    }




    /**
     * Fonction imposée par UserInterface
     *
     * Retourne les roles de l'utilisateur en cherchant dans l'arboressance des roles
     * en fonction des roles sauvé pour cette utilisateur.
     *
     */
    public function getRoles()
    {
        $roles = array();

        /** @var Role $role */
        foreach($this->savedRoles as $role)
        {
            $roles = array_merge($roles,$role->getChildsRecursive(true));
        }


        //il est possible que le user ne soit pas lié a un membre
        if($this->hasMembre())
        {
            /** @var Attribution $attr */
            foreach ($this->getMembre()->getActiveAttributions() as $attr) {
                foreach ($attr->getFonction()->getRoles() as $r)
                    $roles[] = $r;
            }
        }

        /*
         * Conversion des roles en string car si on renvoie
         * des objets, la classe RoleHierarchy de symfony fait
         * merder qqch. Et après tout car marche très bien
         * comme ca aussi ;-)
         */
        $rolesString = array();
        foreach($roles as $role)
        {
            $rolesString[] = $role->getRole();
        }

        return $rolesString;
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
     * Add roles
     *
     * @param \AppBundle\Entity\Role $role
     * @return User
     */
    public function addSavedRole(\AppBundle\Entity\Role $role)
    {

        $this->savedRoles[] = $role;
        return $this;
    }

    /**
     * Remove roles
     *
     * @param \AppBundle\Entity\Role $role
     */
    public function removeSavedRole(\AppBundle\Entity\Role $role)
    {
        $this->savedRoles->removeElement($role);
    }

    /**
     * @return ArrayCollection
     */
    public function getRolesEntity()
    {
        return $this->savedRoles;
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
     * @return ArrayCollection
     */
    public function getSavedRoles(){
        return $this->savedRoles;
    }
}
