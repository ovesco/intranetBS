<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Sender
 *
 * @ORM\Table(name="app_sender")
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="proprietaire", type="string")
 * @ORM\DiscriminatorMap({"membre" = "SenderMembre"})
 */
abstract class Sender
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Mail",
     *                mappedBy="sender", cascade={"persist","remove"})
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
     * Retourne la liste des envois en attente
     *
     * @return array
     */
    public function getNotSentMails()
    {
        $mails = array();
        /** @var Mail $mail */
        foreach($this->mails as $mail)
        {
            if((!$mail->isSentByMail()) && (!$mail->isPrinted()))
            {
                $mails[] = $mail;
            }
        }
        return $mails;
    }

    /**
     * retourne la liste des envois déjà effectué, soit par e-mail ou courrier
     *
     * @return array
     */
    public function getSentMails()
    {
        $mails = array();
        /** @var Mail $mail */
        foreach($this->mails as $mail)
        {
            if($mail->isSentByMail() || $mail->isPrinted())
            {
                $mails[] = $mail;
            }
        }
        return $mails;
    }


    /**
     * Add mail
     *
     * @param Mail $mail
     *
     * @return Sender
     */
    public function addMail(Mail $mail)
    {
        $this->mails[] = $mail;
        if($mail->getSender() != $this)
        {
            $mail->setSender($this);
        }
        return $this;
    }

    /**
     * Remove mail
     *
     * @param Mail $mail
     */
    public function removeMail(Mail $mail)
    {
        $this->mails->removeElement($mail);
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
