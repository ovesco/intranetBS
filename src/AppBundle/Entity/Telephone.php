<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Telephone
 *
 * @ORM\Table(name="app_telephone")
 * @Gedmo\Loggable
 * @ORM\Entity
 */
class Telephone
{
    use RemarquableTrait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=255)
     * @Gedmo\Versioned
     * @Assert\NotBlank()
     */
    private $telephone;

    /**
     * @var Contact
     *
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contact", inversedBy="telephones")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $contact;

    public function __construct($telephone = null)
    {
        $this->setTelephone($telephone);
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
        return  $this->getTelephone();
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
     * Set telephone
     *
     * @param string $telephone
     * @return Telephone
     */
    public function setTelephone($telephone)
    {

        preg_match_all("/[0-9]+/", $telephone, $matches);

        $this->telephone = implode($matches[0]);

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
     * @return Contact
     */
    public function setContact(\AppBundle\Entity\Contact $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

}
