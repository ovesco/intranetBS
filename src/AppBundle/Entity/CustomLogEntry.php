<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;

/**
 * On override la LogEntry des doctrine extensions pour insérer nos propres champs
 *
 * @ORM\Table(
 *     name="ext_log_entries",
 *  indexes={
 *      @ORM\Index(name="log_class_lookup_idx", columns={"object_class"}),
 *      @ORM\Index(name="log_date_lookup_idx", columns={"logged_at"}),
 *      @ORM\Index(name="log_version_lookup_idx", columns={"object_id", "object_class", "version"})
 *  }
 * )
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
