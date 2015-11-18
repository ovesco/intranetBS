<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Membre;

/**
 * SenderMembre
 *
 * @ORM\Entity
 */
class SenderMembre extends Sender
{
    /**
     * @var Membre
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Membre", mappedBy="sender")
     * @ORM\JoinColumn(name="membre_id", referencedColumnName="id")
     */
    private $membre;

    /**
     * Set membre
     *
     * @param Membre $membre
     *
     * @return SenderMembre
     */
    public function setMembre(Membre $membre = null)
    {
        $this->membre = $membre;
        if(is_null($membre->getSender()))
            $membre->setSender($this);
        return $this;
    }

    /**
     * Get membre
     *
     * @return Membre
     */
    public function getMembre()
    {
        return $this->membre;
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->membre;
    }
}
