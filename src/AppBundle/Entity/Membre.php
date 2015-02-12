<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;


//FinancesBundle
Use Interne\FinancesBundle\Entity\CreanceToMembre;
Use Interne\FinancesBundle\Entity\FactureToMembre;

/**
 * Membre
 *
 * @ORM\Entity
 * @ORM\Table(name="app_membres")
 */
class Membre extends Personne implements ExpediableInterface
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Famille", inversedBy="membres", fetch="EAGER")
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
     * @ORM\OneToMany(targetEntity="Interne\FinancesBundle\Entity\CreanceToMembre",
     *                mappedBy="membre", cascade={"persist","remove"})
     */
    private $creances;
    /**
     * @var ArryCollection
     *
     * @ORM\OneToMany(targetEntity="Interne\FinancesBundle\Entity\FactureToMembre",
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
    private $envoiFacture = 'Membre';


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
     * Représentation string du membre
     * On ajoute le numéro BS pour le log de données sur lequel on effectuera des recherches
     *
     * @return string
     */
    public function __toString() {
        return $this->getPrenom() . ' ' . $this->getNom() . ' (' . $this->getNumeroBs() . ')';
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
     * @param CreanceToMembre $creance
     * @return Membre
     */
    public function addCreance(CreanceToMembre $creance)
    {
        $this->creances[] = $creance;
        $creance->setMembre($this);

        return $this;
    }

    /**
     * Remove creance
     *
     * @param CreanceToMembre $creance
     * @return Membre
     */
    public function removeCreance(CreanceToMembre $creance)
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
     * @param FactureToMembre $facture
     * @return Membre
     */
    public function addFacture(FactureToMembre $facture)
    {
        $this->factures[] = $facture;
        $facture->setMembre($this);

        return $this;
    }

    /**
     * Remove facture
     *
     * @param FactureToMembre $facture
     * @return Membre
     */
    public function removeFacture(FactureToMembre $facture)
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

    public function getAdresseExpedition()
    {

        $adresse = $this->getContact()->getAdresse();
        if(!is_null($adresse))
        {
            if ($adresse->isExpediable()) {
                return array('adresse' => $adresse,
                    'owner' => array(
                        'prenom' => $this->getPrenom(),
                        'nom' => $this->getNom(),
                        'type' => 'Membre',
                    ));
            }
        }

        $adresse = $this->getFamille()->getContact()->getAdresse();
        if(!is_null($adresse))
        {
            if ($adresse->isExpediable()) {
                return array('adresse' => $adresse,
                    'owner' => array(
                        'prenom' => null,
                        'nom' => $this->getNom(),
                        'type' => 'Famille',
                    ));
            }
        }

        $mere = $this->getFamille()->getMere();
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
                            'type' => 'Mere',
                        ));
                }
            }
        }

        $pere = $this->getFamille()->getPere();
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
                            'type' => 'Pere',
                        ));
                }
            }
        }


        //aucune adresse trouvée
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
                    $liste['Membre'] = $email->getEmail();
                }

            }
        }


        $emails = $this->getFamille()->getContact()->getEmails();
        if(!is_null($emails))
        {
            foreach($emails as $email){
                if($email->isExpediable())
                {
                    $liste['Famille'] = $email->getEmail();
                }

            }
        }



        $mere = $this->getFamille()->getMere();
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


        $pere = $this->getFamille()->getPere();
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
