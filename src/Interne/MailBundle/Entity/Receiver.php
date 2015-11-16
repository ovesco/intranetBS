<?php

namespace Interne\MailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Receiver
 *
 * @ORM\Table(name="mail_bundle_receiver")
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="proprietaire", type="string")
 * @ORM\DiscriminatorMap({"membre" = "ReceiverMembre", "famille" = "ReceiverFamille"})
 */
abstract class Receiver
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Interne\MailBundle\Entity\Mail",
     *                mappedBy="receiver", cascade={"persist","remove"})
     */
    private $mails;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->mails = new \Doctrine\Common\Collections\ArrayCollection();
    }

    abstract function getOwner();

    /**
     * Add mail
     *
     * @param \Interne\MailBundle\Entity\Mail $mail
     *
     * @return Receiver
     */
    public function addMail(\Interne\MailBundle\Entity\Mail $mail)
    {
        $this->mails[] = $mail;
        if($mail->getReceiver() != $this)
        {
            $mail->setReceiver($this);
        }
        return $this;
    }

    /**
     * Remove mail
     *
     * @param \Interne\MailBundle\Entity\Mail $mail
     */
    public function removeMail(\Interne\MailBundle\Entity\Mail $mail)
    {
        $this->mails->removeElement($mail);
        $mail->setReceiver(null);
    }

    /**
     * Get mails
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMails()
    {
        return $this->mails;
    }
}
