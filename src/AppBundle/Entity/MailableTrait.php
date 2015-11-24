<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


trait MailableTrait
{

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Mail", mappedby="mail cascade={"persist"})
     */
    private $mails;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->mails = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Add mail
     *
     * @param Mail $mail
     *
     * @return Receiver
     */
    public function addMail(Mail $mail)
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
     * @param Mail $mail
     */
    public function removeMail(Mail $mail)
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
