<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Mere
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 */
class Mere extends Geniteur
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->setSexe(Personne::FEMME);
    }
}
