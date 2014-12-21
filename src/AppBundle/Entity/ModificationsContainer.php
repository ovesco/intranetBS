<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ModificationContainer
 *
 * @ORM\Table(name="app_modifications_container")
 * @ORM\Entity
 */
class ModificationsContainer
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
     * @ORM\Column(name="container_key", type="string", length=255)
     */
    private $key;

    /**
     * @var integer
     *
     * @ORM\Column(name="entity_id", type="integer", length=10)
     */
    private $entityId;

    /**
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=255)
     */
    private $class;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Modification", mappedBy="modificationsContainer", cascade={"persist", "remove"}, fetch="EAGER")
     */
    private $modifications;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->modifications = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set class
     *
     * @param string $class
     * @return ModificationContainer
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string 
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Add modifications
     *
     * @param \AppBundle\Entity\Modification $modifications
     * @return ModificationContainer
     */
    public function addModification(\AppBundle\Entity\Modification $modifications)
    {
        $this->modifications[] = $modifications;

        return $this;
    }

    /**
     * Remove modifications
     *
     * @param \AppBundle\Entity\Modification $modifications
     */
    public function removeModification(\AppBundle\Entity\Modification $modifications)
    {
        $this->modifications->removeElement($modifications);
    }


    /**
     * Get modifications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getModifications()
    {
        return $this->modifications;
    }

    /**
     * Set key
     *
     * @param string $key
     * @return ModificationsContainer
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return string 
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set entityId
     *
     * @param integer $entityId
     * @return ModificationsContainer
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Get entityId
     *
     * @return integer 
     */
    public function getEntityId()
    {
        return $this->entityId;
    }
}
