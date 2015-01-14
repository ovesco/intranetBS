<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

//FinancesBundle
Use Interne\FinancesBundle\Entity\Creance;
Use Interne\FinancesBundle\Entity\Facture;

/**
 * Membre
 * @ORM\Entity
 * @ORM\Table(name="app_membres")
 */
class Membre extends Personne
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
     * @var Famille
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Famille", inversedBy="membres")
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
     * @var date
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
     * @var date
     *
     * @ORM\Column(name="inscription", type="date")
     */
    private $inscription;

    /**
     * @var text
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




    /*
     * ====== FinancesBundle =======
     */


    /**
     * @var ArryCollection
     *
     * @ORM\OneToMany(targetEntity="Interne\FinancesBundle\Entity\Creance",
     *                mappedBy="membre", cascade={"persist","remove"})
     */
    private $creances;
    /**
     * @var ArryCollection
     *
     * @ORM\OneToMany(targetEntity="Interne\FinancesBundle\Entity\Facture",
     *                mappedBy="membre", cascade={"persist","remove"})
     */
    private $factures;

    /*
     * Cette propriété détermine si les cérances détenues par ce membre sont facturées
     * à la famille ou au membre lui même.
     */
    /**
     * @var envoiFacture
     *
     * @ORM\Column(name="envoi_facture", type="string", columnDefinition="ENUM('Famille', 'Membre')")
     *
     */
    private $envoiFacture;


    public function __construct()
    {
        $this->inscription = new \Datetime();
        $this->naissance   = new \Datetime();

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
     * Set id
     *
     * @param integer $id
     * @return Membre
     */
    public function setId($id) {

        $this->id = $id;
        return $this;
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
     * Get famille
     *
     * @return Famille
     */
    public function getFamille()
    {
        return $this->famille;
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
     * Get distinctions
     *
     * @return array
     */
    public function getDistinctions()
    {
        return $this->distinctions;
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
     * Get numeroBs
     *
     * @return integer
     */
    public function getNumeroBs()
    {
        return $this->numeroBs;
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
     * Get numeroAvs
     *
     * @return string
     */
    public function getNumeroAvs()
    {
        return $this->numeroAvs;
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
     * Get statut
     *
     * @return string
     */
    public function getStatut()
    {
        return $this->statut;
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
     * Get naissance
     *
     * @return \DateTime
     */
    public function getNaissance()
    {
        return $this->naissance;
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
     * Get inscription
     *
     * @return \DateTime
     */
    public function getInscription()
    {
        return $this->inscription;
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
     * Get active attributions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActiveAttributions()
    {
        $attrs = array();
        $today = new \Datetime("now");

        foreach($this->attributions as $attr) {

            if($attr->getDateFin() >= $today || $attr->getDateFin() == null)
                $attrs[] = $attr;
        }

        return $attrs;
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
     * Retourne l'adresse principale suivant le schéma membre -> famille -> mere -> pere en s'arrêtant
     * à la première facturable
     * @return Adresse
     */
    public function getAdressePrincipale() {

        //On commence par récupérer la première adresse potentielle
        $potentiel = null;

        $adresses = array(
            'membre' => $this->getAdresse(),
            'famille' => $this->getFamille()->getAdresse(),
            'mere' => ($this->getFamille()->getMere() == null) ? null : $this->getFamille()->getMere()->getAdresse(),
            'pere' => ($this->getFamille()->getPere() == null) ? null : $this->getFamille()->getPere()->getAdresse()
        );

        foreach($adresses as $k => $adresse) {

            if(!is_null($adresse)) {

                if ($adresse->isReceivable()) {

                    return array(   'adresse' => $adresse,
                                    'origine' => $k,
                                    'owner' => array(   'prenom' => $this->getPrenom(),
                                                        'nom' => $this->getNom(),
                                                        'class' => 'Membre',
                                                    ));
                }

                if ($potentiel == null)
                    $potentiel = array( 'adresse' => $adresse,
                                        'origine' => $k,
                                        'owner' => array(   'prenom' => $this->getPrenom(),
                                                            'nom' => $this->getNom(),
                                                            'class' => 'Membre',
                                        ));
            }

        }

        return $potentiel;

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
     * Add creance
     *
     * @param Creance $creance
     * @return Membre
     */
    public function addCreance($creance)
    {
        $this->creances[] = $creance;
        $creance->setMembre($this);

        return $this;
    }

    /**
     * Remove creance
     *
     * @param Creance $creance
     * @return Membre
     */
    public function removeCreance($creance)
    {
        $this->creances->remove($creance);
        $creance->setMembre(null);

        return $this;
    }

    /**
     * Set creances
     *
     * @param ArrayCollection $creances
     * @return Membre
     */
    public function setCreances(ArrayCollection $creances)
    {
        $this->creances = $creances;

        foreach($creances as $creance)
        {
            $creance->setMembre($this);
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
     * Set facture
     *
     * @param ArrayCollection $factures
     * @return Membre
     */
    public function setFacture(ArrayCollection $factures)
    {
        $this->factures = $factures;

        foreach($factures as $facture)
        {
            $facture->setMembre($this);
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
     * @param Facture $facture
     * @return Membre
     */
    public function addFacture($facture)
    {
        $this->factures[] = $facture;
        $facture->setMembre($this);

        return $this;
    }

    /**
     * Remove facture
     *
     * @param Facture $facture
     * @return Membre
     */
    public function removeFacture($facture)
    {
        $this->factures->remove($facture);
        $facture->setMembre(null);

        return $this;
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
     * Get envoiFacture
     *
     * @return string
     */
    public function getEnvoiFacture()
    {
        return $this->envoiFacture;
    }




}
