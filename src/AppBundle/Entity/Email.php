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
class Email
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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

    /**
     * @var Contact
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contact", inversedBy="emails")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $contact;

    public function __construct($email = null, $expediable = false)
    {
        $this->email = $email;
        $this->expediable = $expediable;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function __tostring()
    {
        return  $this->getEmail();
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
     * Set email
     *
     * @param string $email
     * @return Email
     */
    public function setEmail($email)
    {

        $this->email = strtolower($email);

        return $this;
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
     * Get expediable
     *
     * @return boolean
     */
    public function getExpediable()
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
     * Get remarques
     *
     * @return string
     */
    public function getRemarques()
    {
        return $this->remarques;
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
     * Get contact
     *
     * @return \AppBundle\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set contact
     *
     * @param \AppBundle\Entity\Contact $contact
     *
     * @return ContactInformation
     */
    public function setContact(\AppBundle\Entity\Contact $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }
}
