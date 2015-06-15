<?php

namespace Interne\HistoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Membre;

/**
 * MemberHistory
 *
 * @ORM\Entity
 * @ORM\Table(name="history_members")
 */
class MemberHistory extends HistoryLogger
{
    /**
     * @var \AppBundle\Entity\Membre
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Membre", inversedBy="historique")
     * @ORM\JoinColumn(name="modified_member_id", referencedColumnName="id")
     */
    private $modifiedMember;

    public function __construct($editor, $modifiedMember, $modifiedProperty, $oldValue = '', $newValue = '')
    {
        $this->setEditor($editor);
        $this->setModifiedMember($modifiedMember);
        $this->setModifiedProperty($modifiedProperty);
        $this->setOldValue($oldValue);
        $this->setNewValue($newValue);
        $this->setDate(new \DateTime('now'));
    }


    /**
     * Set modifiedMember
     *
     * @param \AppBundle\Entity\Membre $modifiedMember
     *
     * @return MemberHistory
     */
    public function setModifiedMember(\AppBundle\Entity\Membre $modifiedMember = null)
    {
        $this->modifiedMember = $modifiedMember;

        return $this;
    }

    /**
     * Get modifiedMember
     *
     * @return \AppBundle\Entity\Membre
     */
    public function getModifiedMember()
    {
        return $this->modifiedMember;
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



}
