<?php

namespace Interne\MailBundle\Entity;

use AppBundle\Entity\Membre;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\ExpediableInterface;
use AppBundle\Entity\Expediable;
use AppBundle\Entity\Adresse;
use AppBundle\Entity\Famille;
/**
 * Mail
 *
 * @ORM\Table(name="mail_bundle_mail")
 * @ORM\Entity
 */
class Mail
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
     * @var \Datetime
     *
     * @ORM\Column(name="shipping_date", type="date", nullable=true)
     */
    private $shippingDate;

    /**
     * @var Document
     *
     * @ORM\ManyToOne(targetEntity="Interne\MailBundle\Entity\Document", inversedBy="mails",cascade={"persist"})
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
     * @param \Interne\MailBundle\Entity\Receiver $receiver
     *
     * @return Mail
     */
    public function setReceiver(\Interne\MailBundle\Entity\Receiver $receiver = null)
    {
        $this->receiver = $receiver;
        if(!is_null($receiver))
        {
            if(!$receiver->getMails()->contains($this))
            {
                $receiver->addMail($this);
            }

            /*
             * Set the adress with the current address of the receiver.
             * But cannot reset this information after the first call
             * of this function.
             *
             * => Then we keep the information if the receiver change his properites...
             */
            if(is_null($this->address))
            {

                /** @var ExpediableInterface $owner */
                $owner = $receiver->getOwner();

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







        }
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

    /**
     * Set shippingDate
     *
     * @param \DateTime $shippingDate
     *
     * @return Mail
     */
    public function setShippingDate($shippingDate)
    {
        $this->shippingDate = $shippingDate;

        return $this;
    }

    /**
     * Get shippingDate
     *
     * @return \DateTime
     */
    public function getShippingDate()
    {
        return $this->shippingDate;
    }

    /**
     * Is sent
     *
     * @return bool
     */
    public function isSent()
    {
        return ($this->shippingDate == null ? false:true);
    }

    /**
     * Set document
     *
     * @param \Interne\MailBundle\Entity\Document $document
     *
     * @return Mail
     */
    public function setDocument(\Interne\MailBundle\Entity\Document $document = null)
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
     * @return \Interne\MailBundle\Entity\Document
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
     * @return array
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
}
