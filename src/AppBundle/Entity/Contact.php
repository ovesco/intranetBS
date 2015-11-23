<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Contact
 *
 * @ORM\Table(name="app_contact")
 * @Gedmo\Loggable
 * @ORM\Entity
 */
class Contact
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
     * @Gedmo\Versioned
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Adresse", mappedBy="contact", cascade={"persist"})
     * @ORM\JoinColumn(name="adresse_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $adresse;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Email", mappedBy="contact", cascade={"persist"})
     */
    private $emails;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Telephone", mappedBy="contact", cascade={"persist"})
     */
    private $telephones;


    public function __construct()
    {
        $this->telephones = new ArrayCollection();
        $this->emails = new ArrayCollection();

        //un contact à forcement une adresse (même vide)
        $this->adresse = new Adresse();

        //un contact à forcement au moins une addresse email
        $this->addEmail(new Email());

        //un contact à forcement au moins un numero de téléphone
        $this->addTelephone(new Telephone());

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
     * Get adresse
     *
     * @return \AppBundle\Entity\Adresse
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set adresse
     *
     * @param \AppBundle\Entity\Adresse $adresse
     * @return Contact
     */
    public function setAdresse(Adresse $adresse = null)
    {
        $this->adresse = $adresse;
        $adresse->setContact($this);

        return $this;
    }

    /**
     * add email
     *
     * @param Email $email
     * @return $this
     */
    public function addEmail(Email $email)
    {
        $this->emails[] = $email;
        $email->setContact($this);
        return $this;
    }

    /**
     * Get email
     *
     * @return ArrayCollection
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * Set emails
     *
     * @param ArrayCollection $emails
     * @return Contact
     */
    public function setEmails(ArrayCollection $emails = null)
    {

        $this->emails = $emails;
        foreach ($emails as $email) {
            $email->setContact($this);
        }

        return $this;
    }

    /**
     * add telephone
     *
     * @param Telephone $telephone
     * @return $this
     */
    public function addTelephone(Telephone $telephone)
    {
        $this->telephones[] = $telephone;
        $telephone->setContact($this);
        return $this;
    }

    /**
     * Get telephones
     *
     * @return ArrayCollection
     */
    public function getTelephones()
    {
        return $this->telephones;
    }

    /**
     * Set telephones
     *
     * @param ArrayCollection $telephones
     * @return Contact
     */
    public function setTelephones(ArrayCollection $telephones = null)
    {
        $this->telephones = $telephones;
        foreach ($telephones as $telephone) {
            $telephone->setContact($this);
        }

        return $this;
    }

    /**
     * Remove email
     *
     * @param \AppBundle\Entity\Email $email
     */
    public function removeEmail(\AppBundle\Entity\Email $email)
    {
        $this->emails->removeElement($email);
    }

    /**
     * Remove telephone
     *
     * @param \AppBundle\Entity\Telephone $telephone
     */
    public function removeTelephone(\AppBundle\Entity\Telephone $telephone)
    {
        $this->telephones->removeElement($telephone);
    }


}
