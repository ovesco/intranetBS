<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactInformation
 *
 * @ORM\MappedSuperclass
 */
abstract class ContactInformation
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
     * @var Contact
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contact", inversedBy="emails")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     */
    private $contact;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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

    /**
     * Get contact
     *
     * @return \AppBundle\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }
}
