<?php

namespace Interne\MailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Membre;

/**
 * Receiver
 *
 * @ORM\Entity
 */
class ReceiverMembre extends Receiver
{
    /**
     * @var Membre
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Membre", mappedBy="receiver")
     * @ORM\JoinColumn(name="membre_id", referencedColumnName="id")
     */
    private $membre;

    /**
     * Set membre
     *
     * @param \AppBundle\Entity\Membre $membre
     *
     * @return ReceiverMembre
     */
    public function setMembre(\AppBundle\Entity\Membre $membre = null)
    {
        $this->membre = $membre;
        if(is_null($membre->getReceiver()))
            $membre->setReceiver($this);
        return $this;
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
}

