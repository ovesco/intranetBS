<?php

namespace Interne\FinancesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Membre;
/**
 * DebiteurMembre
 *
 *
 * @ORM\Entity
 */
class DebiteurMembre extends Debiteur
{
    /**
     * @var Membre
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Membre", mappedBy="debiteur")
     * @ORM\JoinColumn(name="membre_id", referencedColumnName="id")
     */
    private $membre;


    /**
     * Set membre
     *
     * @param \AppBundle\Entity\Membre $membre
     *
     * @return DebiteurMembre
     */
    public function setMembre(\AppBundle\Entity\Membre $membre = null)
    {
        $this->membre = $membre;


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

    /**
     * @return Membre
     */
    public function getOwner()
    {
        return $this->getMembre();
    }

    /**
     * @return string
     */
    public function getOwnerAsString(){
        return $this->getMembre()->getPrenom().' '.$this->getMembre()->getNom();
    }
}
