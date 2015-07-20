<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Interne\FinancesBundle\Entity\DebiteurInterface;


//FinancesBundle

/**
 * Membre
 *
 * @ORM\Entity
 * @ORM\Table(name="app_membres")
 */
class Membre extends Personne implements ExpediableInterface,DebiteurInterface
{

    /**
     * @var Famille
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Famille", inversedBy="membres", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="famille_id", referencedColumnName="id")
     */
    private $famille;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Attribution", mappedBy="membre", cascade={"persist", "remove"})
     */
    private $attributions;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ObtentionDistinction", mappedBy="membre", cascade={"persist", "remove"})
     */
    private $distinctions;

    /**
     * @var \Datetime
     *
     * @ORM\Column(name="naissance", type="date")
     */
    private $naissance;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_bs", type="integer", nullable=true)
     */
    private $numeroBs;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_avs", type="string", length=255, nullable=true)
     */
    private $numeroAvs;

    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", length=255, nullable=true)
     */
    private $statut;

    /**
     * @var \Datetime
     *
     * @ORM\Column(name="inscription", type="date")
     */
    private $inscription;

    /**
     * @var string
     *
     * @ORM\Column(name="remarques", type="text", nullable=true)
     */
    private $remarques;

    /**
     * @var integer
     *
     * @ORM\Column(name="validity", type="integer")
     */
    private $validity;


    /**
     * Return the class name
     * @return string
     */
    static public function className(){
        return __CLASS__;
    }
    /*
     * ====== FinancesBundle =======
     */


    /**
     * @var ArrayCollection
     *
     * @ORM\OneToOne(targetEntity="Interne\FinancesBundle\Entity\DebiteurMembre",
     *                inversedBy="membre", cascade={"persist","remove"})
     */
    private $debiteur;



    /**
     * Cette propriété détermine si les cérances détenues par ce membre sont facturées
     * à la famille ou au membre lui même.
     *
     * @var string envoiFacture
     *
     * @ORM\Column(name="envoi_facture", type="string", columnDefinition="ENUM('Famille', 'Membre')")
     *
     */
    private $envoiFacture = 'Membre';


    /*
     * ===== History Bundle ====
     */
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Interne\HistoryBundle\Entity\MemberHistory", mappedBy="modifiedMember", cascade={"persist","remove"})
     */
    private $historique;


    /*
     * ===== MatBundle
     *
    /*
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Membre", mappedBy="membre", cascade={"persist", "remove"})
     *
    private $bookings;
    //*/

    /**
     * Constructor
     *
     * @param string $prenom
     * @param string $nom Nom de la famille nouvellement créée
     */
    public function __construct($prenom = '', $nom = '')
    {
        $this->inscription = new \Datetime();
        $this->naissance   = new \Datetime();

        /*
         * FinancesBundle
         */
        $this->creances = new ArrayCollection();
        $this->factures = new ArrayCollection();

        $this->validity = true;

        $this->prenom = $prenom;
        $this->famille = new Famille($nom);
    }

    /**
     * Représentation string du membre
     * On ajoute le numéro BS pour le log de données sur lequel on effectuera des recherches
     *
     * @return string
     */
    public function __toString() {
        return ucfirst($this->getPrenom() . ' ' . $this->getNom());
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        if ($this->getFamille() != null)
            return $this->getFamille()->getNom();
        else
            return "Pas dans une famille...";
    }

    /**
     * Get famille
     *
     * @return Famille
     */
    public function getFamille()
    {
        return $this->famille;
    }

    /**
     * Set famille
     *
     * @param Famille $famille
     * @return Membre
     */
    public function setFamille($famille)
    {
        $this->famille = $famille;

        return $this;
    }

    /**
     * Get distinctions
     *
     * @return array
     */
    public function getDistinctions()
    {
        return $this->distinctions;
    }

    /**
     * Set distinctions
     *
     * @param array $distinctions
     * @return Membre
     */
    public function setDistinctions($distinctions)
    {
        $this->distinctions = $distinctions;

        return $this;
    }

    /**
     * Get numeroBs
     *
     * @return integer
     */
    public function getNumeroBs()
    {
        return $this->numeroBs;
    }

    /**
     * Set numeroBs
     *
     * @param integer $numeroBs
     * @return Membre
     */
    public function setNumeroBs($numeroBs)
    {
        $this->numeroBs = $numeroBs;

        return $this;
    }

    /**
     * Get numeroAvs
     *
     * @return string
     */
    public function getNumeroAvs()
    {
        return $this->numeroAvs;
    }

    /**
     * Set numeroAvs
     *
     * @param string $numeroAvs
     * @return Membre
     */
    public function setNumeroAvs($numeroAvs)
    {
        $this->numeroAvs = $numeroAvs;

        return $this;
    }

    /**
     * Get statut
     *
     * @return string
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Set statut
     *
     * @param string $statut
     * @return Membre
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get naissance
     *
     * @return \DateTime
     */
    public function getNaissance()
    {
        return $this->naissance;
    }

    /**
     * Set naissance
     *
     * @param \DateTime $naissance
     * @return Membre
     */
    public function setNaissance($naissance)
    {
        $this->naissance = $naissance;

        return $this;
    }

    /**
     * Get inscription
     *
     * @return \DateTime
     */
    public function getInscription()
    {
        return $this->inscription;
    }

    /**
     * Set inscription
     *
     * @param \DateTime $inscription
     * @return Membre
     */
    public function setInscription($inscription)
    {
        $this->inscription = $inscription;

        return $this;
    }

    /**
     * Add attributions
     *
     * @param \AppBundle\Entity\Attribution $attributions
     * @return Membre
     */
    public function addAttribution(\AppBundle\Entity\Attribution $attributions)
    {
        $this->attributions[] = $attributions;
        $attributions->setMembre($this);
        return $this;
    }

    /**
     * Remove attributions
     *
     * @param \AppBundle\Entity\Attribution $attributions
     */
    public function removeAttribution(\AppBundle\Entity\Attribution $attributions)
    {
        $this->attributions->removeElement($attributions);
    }

    /**
     * Get attributions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttributions()
    {
        return $this->attributions;
    }

    /**
     * Retourne la première attribution active si elle existe, en renvoie une vide sinon pour simplifier l'affichage
     */
    public function getActiveAttribution() {

        $today = new \Datetime("now");
        foreach($this->attributions as $attr) {

            if($attr->getDateFin() >= $today || $attr->getDateFin() == null)
                return $attr;
        }

        $empty = new Attribution;
        $empty->setFonction(new Fonction());
        $empty->setGroupe(new Groupe());

        return $empty;
    }

    /**
     * Retourne un tableau de groupe ou le membre est actuellemnt.
     * @return Groupe[]
     */
    public function getActiveGroupes()
    {
        /** @var Attribution[] $attributions */
        $attributions = $this->getActiveAttributions();

        /** @var Groupe[] $groups */
        $groups = array();

        /** @var Attribution $attribution */
        foreach($attributions as $attribution) {
            $groups[] = $attribution->getGroupe();
        }
        return $groups;
    }

    /**
     * Get active attributions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActiveAttributions()
    {
        $attrs = array();
        $today = new \Datetime("now");

        foreach ($this->attributions as $attr) {

            if ($attr->getDateFin() >= $today || $attr->getDateFin() == null)
                $attrs[] = $attr;
        }

        return $attrs;
    }

    /**
     * Get remarques
     *
     * @return string
     */
    public function getRemarques()
    {
        return $this->remarques;
    }

    /**
     * Set remarques
     *
     * @param $remarques
     * @return Membre
     */
    public function setRemarques($remarques)
    {
        $this->remarques = $remarques;

        return $this;
    }

    public function getUsername()
    {
        // TODO: récupérer le username depuis le User
        return strtolower($this->getPrenom()) . '.' . strtolower($this->getNom());
    }

    /**
     * Add distinction
     *
     * @param \AppBundle\Entity\ObtentionDistinction $distinction
     * @return Membre
     */
    public function addDistinction(\AppBundle\Entity\ObtentionDistinction $distinction)
    {
        $this->distinctions[] = $distinction;
        $distinction->setMembre($this);
        return $this;
    }

    /**
     * Remove distinction
     *
     * @param \AppBundle\Entity\ObtentionDistinction $distinction
     */
    public function removeDistinction(\AppBundle\Entity\ObtentionDistinction $distinction)
    {
        $this->distinctions->removeElement($distinction);
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
     * @return Membre
     */
    public function setValidity($validity)
    {
        $this->validity = $validity;

        return $this;
    }




    /**
     * @param String $className
     * @return bool
     */
    public function isClass($className)
    {
        if($className == 'Membre')
            return true;
        else
            return false;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return 'Membre';
    }


    /**
     * Get envoiFacture
     *
     * @return string
     */
    public function getEnvoiFacture()
    {
        return $this->envoiFacture;
    }

    /**
     * Set envoiFacture
     *
     * @param string $envoiFacture
     * @return Membre
     */
    public function setEnvoiFacture($envoiFacture)
    {
        $this->envoiFacture = $envoiFacture;

        return $this;
    }

    /**
     * Retourne l'adresse d'expédition du membre, c'est-à-dire son adresse principale
     * @return array|null
     */
    public function getAdresseExpedition()
    {
        $expediable = new Expediable($this);
        return $expediable->getAdresse();
    }


    public function getListeEmailsExpedition()
    {
        $expediable = new Expediable($this);
        return $expediable->getListeEmails();
    }


    /**
     * Add historique
     *
     * @param \Interne\HistoryBundle\Entity\MemberHistory $historique
     *
     * @return Membre
     */
    public function addHistorique(\Interne\HistoryBundle\Entity\MemberHistory $historique)
    {
        $this->historique[] = $historique;

        return $this;
    }

    /**
     * Remove historique
     *
     * @param \Interne\HistoryBundle\Entity\MemberHistory $historique
     */
    public function removeHistorique(\Interne\HistoryBundle\Entity\MemberHistory $historique)
    {
        $this->historique->removeElement($historique);
    }

    /**
     * Get historique
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistorique()
    {
        return $this->historique;
    }

    /**
     * Set debiteur
     *
     * @param \Interne\FinancesBundle\Entity\DebiteurMembre $debiteur
     *
     * @return Membre
     */
    public function setDebiteur($debiteur = null)
    {
        $this->debiteur = $debiteur;
        $debiteur->setMembre($this);
        return $this;
    }

    /**
     * Get debiteur
     *
     * @return \Interne\FinancesBundle\Entity\DebiteurMembre
     */
    public function getDebiteur()
    {
        return $this->debiteur;
    }

    /**
     * @param \Interne\FinancesBundle\Entity\Creance $creance
     * @return Membre
     */
    public function addCreance($creance)
    {
        $this->getDebiteur()->addCreance($creance);
        return $this;
    }

    /**
     * @param \Interne\FinancesBundle\Entity\Facture $facture
     * @return Membre
     */
    public function addFacture($facture)
    {
        $this->getDebiteur()->addFacture($facture);
        return $this;
    }

}
