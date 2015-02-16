<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Expediable;

//FinancesBundle
use Interne\FinancesBundle\Entity\CreanceToFamille;
use Interne\FinancesBundle\Entity\FactureToFamille;

/**
 * Famille
 * @ORM\Entity
 * @ORM\Table(name="app_familles")
 */
 
class Famille implements ExpediableInterface, ClassInterface
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Membre", mappedBy="famille", cascade={"persist", "remove"}, fetch="EAGER")
     */
    private $membres;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Pere", mappedBy="famille", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(name="pere_id", referencedColumnName="id")
     */
    private $pere;

    /**
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Mere", mappedBy="famille", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(name="mere_id", referencedColumnName="id")
     */
    private $mere;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min = "2")
     */
    private $nom;

    /**
     * @var integer
     *
     * @ORM\Column(name="validity", type="integer")
     */
    private $validity;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Contact", cascade={"persist", "remove"})
     */
    private $contact;



    /*
     * ====== FinancesBundle =======
     */
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Interne\FinancesBundle\Entity\FactureToFamille",
     *                mappedBy="famille", cascade={"persist"})
     */
    private $factures;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Interne\FinancesBundle\Entity\CreanceToFamille",
     *                mappedBy="famille", cascade={"persist"})
     */
    private $creances;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->membres = new ArrayCollection();

        /*
         * FinancesBundle
         */
        $this->creances = new ArrayCollection();
        $this->factures = new ArrayCollection();
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
     * Get membres
     *
     * @return array
     */
    public function getMembres()
    {
        return $this->membres;
    }

    /**
     * Set pere
     *
     * @param \AppBundle\Entity\Geniteur
     * @return Famille
     */
    public function setPere( \AppBundle\Entity\Geniteur $pere = null)
    {
        $this->pere = $pere;

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
     * Set mere
     *
     * @param  \AppBundle\Entity\Geniteur
     * @return Famille
     */
    public function setMere( \AppBundle\Entity\Geniteur $mere = null)
    {
        $this->mere = $mere;
        return $this;
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
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return ucwords($this->nom);
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
    	$membres->setFamille($this);
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
    public function __toString() {

        $string = "Les " . $this->getNom();

        if ($this->getContact()->getAdresse() != NULL)
            $string .= " de " . $this->getContact()->getAdresse()->getLocalite();

        // . " (" . sizeof($this->getMembres()) . ")";

        return $string;

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
     * Get validity
     *
     * @return integer 
     */
    public function getValidity()
    {
        return $this->validity;
    }

    /*
     * ====== FinancesBundle =======
     */

    /**
     * Is classe
     *
     * @param string $className
     * @return boolean
     */
    public function isClass($className)
    {
        if($className == 'Famille')
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

    /**
     * Set facture
     *
     * @param ArrayCollection $factures
     * @return Famille
     */
    public function setFacture(ArrayCollection $factures)
    {
        $this->factures = $factures;

        foreach($factures as $facture)
        {
            $facture->setFamille($this);
        }

        return $this;
    }

    /**
     * Get facture
     *
     * @return ArrayCollection
     */
    public function getFactures()
    {
        return $this->factures;
    }

    /**
     * Add facture
     *
     * @param FactureToFamille $facture
     * @return Famille
     */
    public function addFacture(FactureToFamille $facture)
    {
        $this->factures[] = $facture;
        $facture->setFamille($this);

        return $this;
    }

    /**
     * Remove facture
     *
     * @param FactureToFamille $facture
     * @return Famille
     */
    public function removeFacture(FactureToFamille $facture)
    {
        $this->factures->remove($facture);
        $facture->setFamille(null);

        return $this;
    }

    /**
     * Add creance
     *
     * @param CreanceToFamille $creance
     * @return Famille
     */
    public function addCreance(CreanceToFamille $creance)
    {
        $this->creances[] = $creance;
        $creance->setFamille($this);

        return $this;
    }

    /**
     * Remove creance
     *
     * @param CreanceToFamille $creance
     * @return Famille
     */
    public function removeCreance(CreanceToFamille $creance)
    {
        $this->creances->remove($creance);
        $creance->setFamille(null);

        return $this;
    }

    /**
     * Set creances
     *
     * @param ArrayCollection $creances
     * @return Famille
     */
    public function setCreances(ArrayCollection $creances)
    {
        $this->creances = $creances;

        foreach($creances as $creance)
        {
            $creance->setFamille($this);
        }

        return $this;
    }

    /**
     * Get creances
     *
     * @return ArrayCollection
     */
    public function getCreances()
    {
        return $this->creances;
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
     * Get contact
     *
     * @return Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    public function getAdresseExpedition()
    {
        $adresse = $this->getContact()->getAdresse();
        if(!is_null($adresse))
        {
            if ($adresse->isExpediable()) {
                return array('adresse' => $adresse,
                    'owner' => array(
                        'prenom' => null,
                        'nom' => $this->getNom(),
                        'class' => 'Famille',
                    ));
            }
        }

        $mere = $this->getMere();
        if(!is_null($mere))
        {
            $adresse = $mere->getContact()->getAdresse();

            if(!is_null($adresse))
            {
                if ($adresse->isExpediable()) {
                    return array('adresse' => $adresse,
                        'owner' => array(
                            'prenom' => $mere->getPrenom(),
                            'nom' => $this->getNom(),
                            'class' => 'Famille',
                        ));
                }
            }
        }

        $pere = $this->getPere();
        if(!is_null($pere))
        {
            $adresse = $pere->getContact()->getAdresse();

            if(!is_null($adresse))
            {
                if ($adresse->isExpediable()) {
                    return array('adresse' => $adresse,
                        'owner' => array(
                            'prenom' => $pere->getPrenom(),
                            'nom' => $this->getNom(),
                            'class' => 'Famille',
                        ));
                }
            }
        }

        return null;
    }

    public function getListeEmailsExpedition()
    {
        $liste = array();


        $emails = $this->getContact()->getEmails();
        if(!is_null($emails))
        {
            foreach($emails as $email){
                if($email->isExpediable())
                {
                    $liste['Famille'] = $email->getEmail();
                }

            }
        }



        $mere = $this->getMere();
        if(!is_null($mere))
        {
            $emails = $mere->getContact()->getEmails();
            if(!is_null($emails))
            {
                foreach($emails as $email){
                    if($email->isExpediable())
                    {
                        $liste['Mère'] = $email->getEmail();
                    }

                }
            }
        }


        $pere = $this->getPere();
        if(!is_null($pere))
        {
            $emails = $pere->getContact()->getEmails();
            if(!is_null($emails))
            {
                foreach($emails as $email){
                    if($email->isExpediable())
                    {
                        $liste['Père'] = $email->getEmail();
                    }

                }
            }
        }





        return $liste;
    }



}
