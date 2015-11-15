<?php

namespace Interne\MailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mail
 *
 * @ORM\Table(name="mail_bundle_mail")
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"post" = "MailPost", "electronic" = "MailElectronic"})
 */
abstract class Mail
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var Receiver
     *
     * @ORM\ManyToOne(targetEntity="Interne\MailBundle\Entity\Receiver", inversedBy="mails")
     * @ORM\JoinColumn(name="receiver_id", referencedColumnName="id")
     */
    private $receiver;


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
     * Set title
     *
     * @param string $title
     *
     * @return Mail
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set receiver
     *
     * @param \Interne\MailBundle\Entity\Receiver $receiver
     *
     * @return Mail
     */
    public function setReceiver(\Interne\MailBundle\Entity\Receiver $receiver = null)
    {
        $this->receiver = $receiver;

        return $this;
    }

    /**
     * Get receiver
     *
     * @return \Interne\MailBundle\Entity\Receiver
     */
    public function getReceiver()
    {
        return $this->receiver;
    }
}
