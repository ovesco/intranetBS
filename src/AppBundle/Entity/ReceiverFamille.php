<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReceiverFamille
 *
 * @ORM\Entity
 */
class ReceiverFamille extends Receiver
{
    /**
     * @var Famille
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Famille", mappedBy="receiver")
     * @ORM\JoinColumn(name="famille_id", referencedColumnName="id")
     */
    private $famille;

    /**
     * Set famille
     *
     * @param Famille $famille
     *
     * @return ReceiverFamille
     */
    public function setFamille(Famille $famille = null)
    {
        $this->famille = $famille;
        if(is_null($famille->getReceiver()))
            $famille->setReceiver($this);
        return $this;
    }

    /**
     * Get famille
     *
     * @return Famille
     */
    public function getFamille()
    {
        return $this->famille;
    }

    /**
     * @return ExpediableInterface
     */
    public function getOwner()
    {
        return $this->famille;
    }
}

