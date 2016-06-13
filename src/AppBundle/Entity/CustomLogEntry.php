<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;

/**
 * On override la LogEntry des doctrine extensions pour ins�rer nos propres champs
 *
 * @ORM\Entity(repositoryClass="Gedmo\Loggable\Entity\Repository\LogEntryRepository")
 */
class CustomLogEntry extends AbstractLogEntry
{
    public static $WAITING  = 0;
    public static $ACCEPTED = 1;
    public static $REFUSED = -1;

    /**
     * @var integer $status
     *
     * @ORM\Column(type="integer")
     */
    protected $status;

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return CustomLogEntry
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }
}
