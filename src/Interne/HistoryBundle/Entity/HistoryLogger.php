<?php

namespace Interne\HistoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Membre;

/**
 * @ORM\MappedSuperclass
 */
abstract class HistoryLogger
{
    /**
     * @var Integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Membre
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Membre")
     * @ORM\JoinColumn(name="editor_id", referencedColumnName="id")
     */
    protected $editor;

    /**
     * @var String
     *
     * @ORM\Column(name="session", type="string", length=255)
     */
    protected $session;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    protected $date;

    /**
     * @var String
     *
     * @ORM\Column(name="modified_proprety", type="string", length=255)
     */
    protected $modifiedProperty;

    /**
     * @var String
     *
     * @ORM\Column(name="old_value", type="string", length=255, nullable=true)
     */
    protected $oldValue;

    /**
     * @var String
     *
     * @ORM\Column(name="new_value", type="string", length=255, nullable=true)
     */
    protected $newValue;


    /**
     * Set editor
     *
     * @param \AppBundle\Entity\Membre $editor
     * @return MemberHistory
     */
    public function setEditor(\AppBundle\Entity\Membre $editor = null)
    {
        $this->editor = $editor;

        return $this;
    }

    /**
     * Get editor
     *
     * @return \AppBundle\Entity\Membre
     */
    public function getEditor()
    {
        return $this->editor;
    }

    /**
     * Set session
     *
     * @param string $session
     * @return MemberHistory
     */
    public function setSession($session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get session
     *
     * @return string
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return MemberHistory
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
     * Set modifiedProperty
     *
     * @param string $modifiedProperty
     *
     * @return MemberHistory
     */
    public function setModifiedProperty($modifiedProperty)
    {
        $this->modifiedProperty = $modifiedProperty;

        return $this;
    }

    /**
     * Get modifiedProperty
     *
     * @return string
     */
    public function getModifiedProperty()
    {
        return $this->modifiedProperty;
    }

    /**
     * Set oldValue
     *
     * @param string $oldValue
     *
     * @return MemberHistory
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
     *
     * @return MemberHistory
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
