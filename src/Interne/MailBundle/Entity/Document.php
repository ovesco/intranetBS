<?php

namespace Interne\MailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Document
 *
 * @ORM\Table(name="mail_bundle_document")
 * @ORM\Entity
 */
class Document
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
     * @ORM\Column(name="file",type="string")
     *
     * @Assert\NotBlank(message="Please, upload the a file.")
     * @Assert\File(mimeTypes={ "application/pdf" })
     */
    private $file;

    /**
     * @ORM\Column(name="name",type="string", length=255)
     *
     */
    private $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Interne\MailBundle\Entity\Mail",
     *                mappedBy="document", cascade={"persist"})
     */
    private $mails;


    public function __construct(){
        $this->mails = new ArrayCollection();
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

    /**
     * Add mail
     *
     * @param \Interne\MailBundle\Entity\Mail $mail
     *
     * @return Document
     */
    public function addMail(\Interne\MailBundle\Entity\Mail $mail)
    {
        $this->mails[] = $mail;
        if($mail->getDocument() != $this)
        {
            $mail->setDocument($this);
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

    /**
     * Set file
     *
     * @param string $file
     *
     * @return Document
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Document
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
