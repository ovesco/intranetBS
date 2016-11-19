<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Entity\DebiteurFamille;
use FOS\ElasticaBundle\Configuration\Search;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;


/**
 * Famille
 *
 * @Gedmo\Loggable
 * @ORM\Entity
 * @ORM\Table(name="app_familles")
 * @Search(repositoryClass="AppBundle\Search\Famille\FamilleRepository")
 *
 * @ExclusionPolicy("all")
 *
 */
class Famille implements ExpediableInterface,ClassNameInterface,DebiteurInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Expose
     *
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Membre", mappedBy="famille", cascade={"persist"})
     * @ORM\JoinColumn(name="membres_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @Expose
     */
    private $membres;

    /**
     * @Gedmo\Versioned
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Pere", mappedBy="famille", cascade={"persist"})
     * @ORM\JoinColumn(name="pere_id", referencedColumnName="id", onDelete="SET NULL")
     *
     *
     */
    private $pere;

    /**
     *
     * @Gedmo\Versioned
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Mere", mappedBy="famille", cascade={"persist"})
     * @ORM\JoinColumn(name="mere_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $mere;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="nom", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min = "2")
     *
     * @Expose
     */
    private $nom;

    /**
     * @var integer
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="validity", type="integer")
     */
    private $validity;

    /**
     * @Gedmo\Versioned
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Contact", cascade={"persist"})
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $contact;



    /**
     * @var DebiteurFamille
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\DebiteurFamille",
     *                inversedBy="famille", cascade={"persist","remove"})
     */
    private $debiteur;

    /**
     * @var ReceiverFamille
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\ReceiverFamille",
     *                inversedBy="famille", cascade={"persist","remove"})
     */
    private $receiver;


    /**
     * Constructor
     *
     * @param string $nom
     */
    public function __construct($nom = '')
    {
        $this->membres = new ArrayCollection();

        //une famille a forcement un contact
        $this->contact = new Contact();

        //une famille a forcement un debiteur
        $this->debiteur = new DebiteurFamille();
        $this->debiteur->setFamille($this);

        //un membre a forcement un receiver
        $this->receiver = new ReceiverFamille();
        $this->receiver->setFamille($this);

        $this->validity = true;

        $this->nom = $nom;
    }

    /**
     * Return the class name
     * @return string
     */
    static public function className(){
        return __CLASS__;
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
     * Get self
     * @return famille
     */
    public function getSelf() {

        return $this;
    }

    /**
     * Add membres
     *
     * @param \AppBundle\Entity\Membre $membres
     * @return Famille
     */
    public function addMembre(\AppBundle\Entity\Membre $membres)
    {
        $this->membres[] = $membres;
        if($membres->getFamille() != $this)
        {
            $membres->setFamille($this);
        }
        return $this;
    }

    /**
     * Remove membres
     *
     * @param \AppBundle\Entity\Membre $membres
     */
    public function removeMembre(\AppBundle\Entity\Membre $membres)
    {
        $this->membres->removeElement($membres);
    }

    /**
     * Doit renvoyer quelque chose qui permet d'identifier (humainement) une famille
     * Le nom n'est pas suffisant p.ex puisqu'il peut y avoir plusieurs famille avec le même nom
     * du coup on renvoie la localité derrière
     *
     * @return string
     */
    public function __toString()
    {
        $string = "Les " . $this->getNom();

        if ($this->getContact() != null)
        {
            if ($this->getContact()->getAdresse() != NULL) {
                $string .= " de " . $this->getContact()->getAdresse()->getLocalite();
            }
        }

        return $string;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return ucwords($this->nom);
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Famille
     */
    public function setNom($nom)
    {
        $this->nom = ucwords($nom);

        return $this;
    }

    /**
     * Get contact
     *
     * @return Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set contact
     *
     * @param Contact $contact
     * @return Famille
     */
    public function setContact(Contact $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get validity
     *
     * @return integer
     */
    public function getValidity()
    {
        return $this->validity;
    }

    /**
     * Set validity
     *
     * @param integer $validity
     * @return Famille
     */
    public function setValidity($validity)
    {
        $this->validity = $validity;

        return $this;
    }

    /**
     * Is classe
     *
     * @param string $className
     * @return boolean
     */
    public function isClass($className)
    {
        if ($className == 'Famille')
            return true;
        else
            return false;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return 'Famille';
    }



    public function getAdresseExpedition()
    {
        $expediable = new Expediable($this);
        return $expediable->getAdresse();
    }

    /**
     * Get mere
     *
     * @return \AppBundle\Entity\Geniteur
     */
    public function getMere()
    {
        return $this->mere;
    }

    /**
     * Set mere
     *
     * @param  Mere $mere
     * @return Famille
     */
    public function setMere($mere = null)
    {
        $this->mere = $mere;
        if ($mere != null) {
            $mere->setFamille($this);
        }
        return $this;
    }

    /**
     * Get pere
     *
     * @return \AppBundle\Entity\Geniteur
     */
    public function getPere()
    {
        return $this->pere;
    }

    /**
     * Set pere
     *
     * @param Pere $pere
     * @return Famille
     */
    public function setPere($pere = null)
    {
        $this->pere = $pere;
        if ($pere != null) {
            $pere->setFamille($this);
        }
        return $this;
    }

    public function getListeEmailsExpedition()
    {
        $expediable = new Expediable($this);
        return $expediable->getListeEmails();
    }

    public function getListePrenomEnfants()
    {
        $enfants = $this->getMembres();

        $listOfPrenom = array();
        foreach ($enfants as $enfant)
        {
            /** @var Membre $enfant */
            $listOfPrenom[] = $enfant->getPrenom();
        }
        return $listOfPrenom;
    }

    /**
     * Get membres
     *
     * @return ArrayCollection
     */
    public function getMembres()
    {
        return $this->membres;
    }



    /**
     * Get debiteur
     *
     * @return \AppBundle\Entity\DebiteurFamille
     */
    public function getDebiteur()
    {
        return $this->debiteur;
    }

    /**
     * Set debiteur
     *
     * @param \AppBundle\Entity\DebiteurFamille $debiteur
     *
     * @return Famille
     */
    public function setDebiteur($debiteur = null)
    {
        $this->debiteur = $debiteur;
        $debiteur->setFamille($this);
        return $this;
    }



    /**
     * Set receiver
     *
     * @param ReceiverFamille $receiver
     *
     * @return Famille
     */
    public function setReceiver(ReceiverFamille $receiver = null)
    {
        $this->receiver = $receiver;
        if(is_null($receiver->getFamille()))
            $receiver->setFamille($this);
        return $this;
    }

    /**
     * Get receiver
     *
     * @return ReceiverFamille
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    public function addCreance(Creance $creance)
    {
        $this->getDebiteur()->addCreance($creance);
        return $this;
    }

    public function addFacture(Facture $facture)
    {
        $this->getDebiteur()->addFacture($facture);
        return $this;
    }

}
