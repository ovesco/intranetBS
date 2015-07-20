<?php

namespace Interne\FinancesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Famille;

/**
 * DebiteurFamille
 *
 *
 * @ORM\Entity
 */
class DebiteurFamille extends Debiteur
{
    /**
     * @var Famille
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Famille", mappedBy="debiteur")
     * @ORM\JoinColumn(name="famille_id", referencedColumnName="id")
     */
    private $famille;

    /**
     * Set famille
     *
     * @param \AppBundle\Entity\Famille $famille
     *
     * @return DebiteurFamille
     */
    public function setFamille(\AppBundle\Entity\Famille $famille = null)
    {
        $this->famille = $famille;

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

    public function getOwner()
    {
        return $this->getFamille();
    }
}