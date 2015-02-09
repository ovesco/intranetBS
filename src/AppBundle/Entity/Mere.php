<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Mere
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 */
class Mere extends Geniteur
{
    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Famille", inversedBy="mere", cascade={"persist"})
     */
    private $famille;

    /**
     * Set famille
     *
     * @param Famille $famille
     * @return Mere
     */
    public function setFamille($famille)
    {
        $this->famille = $famille;

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
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        if($this->nom == null)
        {

            return $this->getFamille()->getNom();
        }
        else
            return ucwords($this->nom);
    }

}