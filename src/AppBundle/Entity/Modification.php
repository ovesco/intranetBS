<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Modification
 *
 * @ORM\Table(name="app_modifications")
 * @ORM\Entity
 */
class Modification
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
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="oldValue", type="string", length=255, nullable=true)
     */
    private $oldValue;

    /**
     * @var string
     *
     * @ORM\Column(name="newValue", type="string", length=255, nullable=true)
     */
    private $newValue;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var ModificationsContainer
     *
     * @ORM\ManyToOne(targetEntity="ModificationsContainer", inversedBy="modifications")
     */
    private $modificationsContainer;


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
     * Set path
     *
     * @param string $path
     * @return Modification
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Modification
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set modificationsContainer
     *
     * @param \AppBundle\Entity\ModificationsContainer $modificationsContainer
     * @return Modification
     */
    public function setModificationsContainer(\AppBundle\Entity\ModificationsContainer $modificationsContainer = null)
    {
        $this->modificationsContainer = $modificationsContainer;
        $this->modificationsContainer->addModification($this);
        return $this;
    }

    /**
     * Get modificationsContainer
     *
     * @return \AppBundle\Entity\ModificationsContainer
     */
    public function getModificationsContainer()
    {
        return $this->modificationsContainer;
    }

    /**
     * Set oldValue
     *
     * @param string $oldValue
     * @return Modification
     */
    public function setOldValue($oldValue)
    {
        $this->oldValue = $oldValue;

        return $this;
    }

    /**
     * Get oldValue
     *
     * @return string 
     */
    public function getOldValue()
    {
        return $this->oldValue;
    }

    /**
     * Set newValue
     *
     * @param string $newValue
     * @return Modification
     */
    public function setNewValue($newValue)
    {
        $this->newValue = $newValue;

        return $this;
    }

    /**
     * Get newValue
     *
     * @return string 
     */
    public function getNewValue()
    {
        return $this->newValue;
    }
}
