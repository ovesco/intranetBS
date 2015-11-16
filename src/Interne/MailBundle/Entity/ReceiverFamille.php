<?php

namespace Interne\MailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Famille;

use AppBundle\Entity\ExpediableInterface;
/**
 * Receiver
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
     * @param \AppBundle\Entity\Famille $famille
     *
     * @return ReceiverFamille
     */
    public function setFamille(\AppBundle\Entity\Famille $famille = null)
    {
        $this->famille = $famille;
        if(is_null($famille->getReceiver()))
            $famille->setReceiver($this);
        return $this;
    }

    /**
     * Get famille
     *
     * @return \AppBundle\Entity\Famille
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

