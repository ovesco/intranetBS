<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class Mere
 * @Gedmo\Loggable
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 */
class Mere extends Geniteur
{


    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Famille", inversedBy="mere")
     * @ORM\JoinColumn(name="famille_id", referencedColumnName="id", onDelete="CASCADE")
     * @Gedmo\Versioned
     */
    protected $famille;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->setSexe(Personne::FEMME);
    }
}
