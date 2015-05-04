<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Email
 *
 * @ORM\Table(name="app_email")
 * @ORM\Entity
 */
class Email extends ContactInformation
{

    /**
     * @var boolean
     * @ORM\Column(name="expediable", type="boolean")
     */
    private $expediable;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="remarques", type="text", nullable=true)
     */
    private $remarques;

    public function __construct($email = null, $expediable = false)
    {
        $this->email = $email;
        $this->expediable = $expediable;
    }

    public function __tostring()
    {
        return  $this->getEmail();
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Email
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }


    /**
     * is expediable
     *
     * @return bool
     */
    public function isExpediable()
    {
        return $this->expediable;
    }

    /**
     * Set expediable
     *
     * @param boolean $expediable
     * @return Email
     */
    public function setExpediable($expediable)
    {
        $this->expediable = $expediable;

        return $this;
    }

    /**
     * Get expediable
     *
     * @return boolean
     */
    public function getExpediable()
    {
        return $this->expediable;
    }

    /**
     * Set remarques
     *
     * @param string $remarques
     * @return Email
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
