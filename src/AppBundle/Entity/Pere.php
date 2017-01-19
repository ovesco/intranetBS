<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Pere
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 */
class Pere extends Geniteur
{


    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Famille", inversedBy="pere")
     * @ORM\JoinColumn(name="famille_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $famille;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->setSexe(Personne::HOMME);
    }
}
