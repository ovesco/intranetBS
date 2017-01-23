<?php

namespace AppBundle\Entity;
use Gedmo\Mapping\Annotation as Gedmo;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Pere
 * @package AppBundle\Entity
 *
 * @Gedmo\Loggable
 * @ORM\Entity
 */
class Pere extends Geniteur
{


    /**
     * @Gedmo\Versioned
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
        parent::__construct();
        $this->setSexe(Personne::HOMME);
    }
}
