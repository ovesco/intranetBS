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
     * Constructor
     *
     */
    public function __construct()
    {
        $this->setSexe(Personne::HOMME);
    }

}
