<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Telephone
 *
 * @ORM\Table(name="app_telephone")
 * @ORM\Entity
 */
class Telephone extends ContactInformation
{

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="remarques", type="text", nullable=true)
     */
    private $remarques;

    public function __construct($telephone = null)
    {
        $this->telephone = $telephone;
    }

    public function __tostring()
    {
        return  $this->getTelephone();
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return Telephone
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set remarques
     *
     * @param string $remarques
     * @return Telephone
     */
    public function setRemarques($remarques)
    {
        $this->remarques = $remarques;

        return $this;
    }

    /**
     * Get remarques
     *
     * @return string
     */
    public function getRemarques()
    {
        return $this->remarques;
    }



}
