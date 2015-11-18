<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReceiverMembre
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
     * @param Membre $membre
     *
     * @return ReceiverMembre
     */
    public function setMembre(Membre $membre = null)
    {
        $this->membre = $membre;
        if(is_null($membre->getReceiver()))
            $membre->setReceiver($this);
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
     * @return ExpediableInterface
     */
    public function getOwner()
    {
        return $this->membre;
    }
}

