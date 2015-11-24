<?php

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;



/**
 * Mail
 *
 * @ORM\Table(name="app_mail")
 * @ORM\Entity
 */
class Mail
{


    private $mailable;

    const METHOD_EMAIL = "email";
    const METHOD_POST = "post";
    const METHOD_EMAIL_AND_POST = "email_and_post";
    const METHOD_DEFAULT = Mail::METHOD_EMAIL_AND_POST;


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
     * @var string
     *
     * @ORM\Column(name="method", type="string", length=255)
     */
    private $method;


    /**
     * @var Sender
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Sender", inversedBy="mails")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id")
     */
    private $sender;

    /**
     * @var Receiver
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Receiver", inversedBy="mails")
     * @ORM\JoinColumn(name="receiver_id", referencedColumnName="id")
     */
    private $receiver;





    /**
     * @var \Datetime
     *
     * @ORM\Column(name="last_email_sent_date", type="date", nullable=true)
     */
    private $lastEmailSentDate;

    /**
     * @var \Datetime
     *
     * @ORM\Column(name="last_print_date", type="date", nullable=true)
     */
    private $lastPrintDate;

    /**
     * @var Document
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Document", inversedBy="mails",cascade={"persist"})
     * @ORM\JoinColumn(name="document_id", referencedColumnName="id")
     */
    private $document;

    /**
     * @var ArrayCollection
     *
     * @ORM\Column(name="emails", type="array")
     */
    private $emails;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="text", nullable=true)
     */
    private $address;

    public function __construct(){
        $this->emails = new ArrayCollection();
        $this->setMethod(Mail::METHOD_DEFAULT);
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
     * @param Receiver $receiver
     *
     * @return Mail
     */
    public function setReceiver(Receiver $receiver = null)
    {
        $this->receiver = $receiver;
        if(!is_null($receiver))
        {

            if(!$receiver->getMails()->contains($this))
            {
                $receiver->addMail($this);
            }

            /** @var ExpediableInterface $owner */
            $owner = $receiver->getOwner();

            if(!is_null($owner))
            {

                /*
                 * Set the adress with the current address of the receiver.
                 * But cannot reset this information after the first call
                 * of this function.
                 *
                 * => Then we keep the information if the receiver change his properites...
                 */
                if(is_null($this->address) && (($this->method == Mail::METHOD_POST) || ($this->method == Mail::METHOD_EMAIL_AND_POST)))
                {

                    /** @var Adresse $address */
                    $address = $owner->getAdresseExpedition()[Expediable::ADDRESS];

                    $expediableEntity = $owner->getAdresseExpedition()[Expediable::OWNER_ENTITY];

                    if($expediableEntity instanceof Membre)
                    {
                        $header = $expediableEntity->getNom().' '.$expediableEntity->getPrenom();
                        $this->setAddress($header.PHP_EOL.$address->toPostalFormat());
                    }
                    if($expediableEntity instanceof Famille)
                    {
                        $header = 'Famille '.$expediableEntity->getNom();
                        $this->setAddress($header.PHP_EOL.$address->toPostalFormat());
                    }
                }
                if($this->emails->isEmpty() && (($this->method == Mail::METHOD_EMAIL) || ($this->method == Mail::METHOD_EMAIL_AND_POST)))
                {
                    $emails = $owner->getListeEmailsExpedition();
                    foreach($emails as $email)
                    {
                        $this->emails->add($email[Expediable::EMAIL]);
                    }
                }
            }


        }
        return $this;
    }

    /**
     * Get receiver
     *
     * @return Receiver
     */
    public function getReceiver()
    {
        return $this->receiver;
    }


    /**
     * IsSentByMail
     *
     * @return bool
     */
    public function isSentByMail()
    {
        return ($this->lastEmailSentDate == null ? false:true);
    }

    /**
     * @return bool
     */
    public function isPrinted()
    {
        return ($this->lastPrintDate == null ? false:true);
    }

    /**
     * Set document
     *
     * @param Document $document
     *
     * @return Mail
     */
    public function setDocument(Document $document = null)
    {
        $this->document = $document;
        if(!is_null($document))
        {
            if(!$document->getMails()->contains($this))
            {
                $document->addMail($this);
            }
        }
        return $this;
    }

    /**
     * Get document
     *
     * @return Document
     */
    public function getDocument()
    {
        return $this->document;
    }


    public function addEmail($email)
    {
        $this->emails->add($email);
    }

    /**
     * Set emails
     *
     * @param array $emails
     *
     * @return Mail
     */
    public function setEmails($emails)
    {
        $this->emails = $emails;

        return $this;
    }

    /**
     * Get emails
     *
     * @return ArrayCollection
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Mail
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }


    /**
     * Set sender
     *
     * @param Sender $sender
     *
     * @return Mail
     */
    public function setSender(Sender $sender = null)
    {
        $this->sender = $sender;
        if(!$sender->getMails()->contains($this))
        {
            $sender->addMail($this);
        }
        return $this;
    }

    /**
     * Get sender
     *
     * @return Sender
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set lastEmailSentDate
     *
     * @param \DateTime $lastEmailSentDate
     *
     * @return Mail
     */
    public function setLastEmailSentDate($lastEmailSentDate)
    {
        $this->lastEmailSentDate = $lastEmailSentDate;

        return $this;
    }

    /**
     * Get lastEmailSentDate
     *
     * @return \DateTime
     */
    public function getLastEmailSentDate()
    {
        return $this->lastEmailSentDate;
    }

    /**
     * Set lastPrintDate
     *
     * @param \DateTime $lastPrintDate
     *
     * @return Mail
     */
    public function setLastPrintDate($lastPrintDate)
    {
        $this->lastPrintDate = $lastPrintDate;

        return $this;
    }

    /**
     * Get lastPrintDate
     *
     * @return \DateTime
     */
    public function getLastPrintDate()
    {
        return $this->lastPrintDate;
    }

    /**
     * Set method
     *
     * @param string $method
     *
     * @return Mail
     */
    public function setMethod($method)
    {
        switch($method){
            case Mail::METHOD_EMAIL:
            case Mail::METHOD_POST:
            case Mail::METHOD_EMAIL_AND_POST:
                $this->method = $method;
                break;
            case Mail::METHOD_DEFAULT:
            Default:
                $this->method = Mail::METHOD_DEFAULT;
        }
        $this->method = $method;

        return $this;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
}
