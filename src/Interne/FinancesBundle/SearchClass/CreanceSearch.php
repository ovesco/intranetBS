<?php

namespace Interne\FinancesBundle\SearchClass;

/**
 * Class CreanceSearch
 * @package Interne\FinancesBundle\SearchClass
 */
class CreanceSearch
{
    /**
     * @var integer
     *
     */
    private $id;

    /**
     * @var string
     *
     */
    private $titre;

    /**
     * @var string
     *
     */
    private $remarque;

    /**
     * @var \DateTime
     *
     */
    private $toDateCreation;

    /**
     * @var \DateTime
     *
     */
    private $fromDateCreation;

    /**
     * @var float
     *
     */
    private $toMontantEmis;

    /**
     * @var float
     *
     */
    private $fromMontantEmis;

    /**
     * @var float
     *
     */
    private $toMontantRecu;

    /**
     * @var float
     *
     */
    private $fromMontantRecu;

    /**
     * @var string
     *
     */
    private $prenomMembre;

    /**
     * @var string
     *
     */
    private $nomMembre;

    /**
     * @var string
     *
     */
    private $nomFamille;


    /**
     * @var integer
     *
     */
    private $idFacture;

    /**
     * @var string
     *
     */
    private $factured;

    /**
     * @var string
     *
     */
    private $payed;

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
     * Set titre
     *
     * @param string $titre
     *
     * @return CreanceSearch
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set remarque
     *
     * @param string $remarque
     *
     * @return CreanceSearch
     */
    public function setRemarque($remarque)
    {
        $this->remarque = $remarque;

        return $this;
    }

    /**
     * Get remarque
     *
     * @return string
     */
    public function getRemarque()
    {
        return $this->remarque;
    }

    /**
     * Set toDateCreation
     *
     * @param \DateTime $toDateCreation
     *
     * @return CreanceSearch
     */
    public function setToDateCreation($toDateCreation)
    {
        $this->toDateCreation = $toDateCreation;

        return $this;
    }

    /**
     * Get toDateCreation
     *
     * @return \DateTime
     */
    public function getToDateCreation()
    {
        return $this->toDateCreation;
    }

    /**
     * Set fromDateCreation
     *
     * @param \DateTime $fromDateCreation
     *
     * @return CreanceSearch
     */
    public function setFromDateCreation($fromDateCreation)
    {
        $this->fromDateCreation = $fromDateCreation;

        return $this;
    }

    /**
     * Get fromDateCreation
     *
     * @return \DateTime
     */
    public function getFromDateCreation()
    {
        return $this->fromDateCreation;
    }

    /**
     * Set toMontantEmis
     *
     * @param float $toMontantEmis
     *
     * @return CreanceSearch
     */
    public function setToMontantEmis($toMontantEmis)
    {
        $this->toMontantEmis = $toMontantEmis;

        return $this;
    }

    /**
     * Get toMontantEmis
     *
     * @return float
     */
    public function getToMontantEmis()
    {
        return $this->toMontantEmis;
    }

    /**
     * Set fromMontantEmis
     *
     * @param float $fromMontantEmis
     *
     * @return CreanceSearch
     */
    public function setFromMontantEmis($fromMontantEmis)
    {
        $this->fromMontantEmis = $fromMontantEmis;

        return $this;
    }

    /**
     * Get fromMontantEmis
     *
     * @return float
     */
    public function getFromMontantEmis()
    {
        return $this->fromMontantEmis;
    }

    /**
     * Set toMontantRecu
     *
     * @param float $toMontantRecu
     *
     * @return CreanceSearch
     */
    public function setToMontantRecu($toMontantRecu)
    {
        $this->toMontantRecu = $toMontantRecu;

        return $this;
    }

    /**
     * Get toMontantRecu
     *
     * @return float
     */
    public function getToMontantRecu()
    {
        return $this->toMontantRecu;
    }

    /**
     * Set fromMontantRecu
     *
     * @param float $fromMontantRecu
     *
     * @return CreanceSearch
     */
    public function setFromMontantRecu($fromMontantRecu)
    {
        $this->fromMontantRecu = $fromMontantRecu;

        return $this;
    }

    /**
     * Get fromMontantRecu
     *
     * @return float
     */
    public function getFromMontantRecu()
    {
        return $this->fromMontantRecu;
    }

    /**
     * Set idFacture
     *
     * @param integer $idFacture
     *
     * @return CreanceSearch
     */
    public function setIdFacture($idFacture)
    {
        $this->idFacture = $idFacture;

        return $this;
    }

    /**
     * Get idFacture
     *
     * @return integer
     */
    public function getIdFacture()
    {
        return $this->idFacture;
    }

    /**
     * Set prenomMembre
     *
     * @param string $prenomMembre
     *
     * @return CreanceSearch
     */
    public function setPrenomMembre($prenomMembre)
    {
        $this->prenomMembre = $prenomMembre;

        return $this;
    }

    /**
     * Get prenomMembre
     *
     * @return string
     */
    public function getPrenomMembre()
    {
        return $this->prenomMembre;
    }

    /**
     * Set nomMembre
     *
     * @param string $nomMembre
     *
     * @return CreanceSearch
     */
    public function setNomMembre($nomMembre)
    {
        $this->nomMembre = $nomMembre;

        return $this;
    }

    /**
     * Get nomMembre
     *
     * @return string
     */
    public function getNomMembre()
    {
        return $this->nomMembre;
    }

    /**
     * Set nomFamille
     *
     * @param string $nomFamille
     *
     * @return CreanceSearch
     */
    public function setNomFamille($nomFamille)
    {
        $this->nomFamille = $nomFamille;

        return $this;
    }

    /**
     * Get nomFamille
     *
     * @return string
     */
    public function getNomFamille()
    {
        return $this->nomFamille;
    }


    /**
     * Set factured
     *
     * @param string $factured
     *
     * @return CreanceSearch
     */
    public function setFactured($factured)
    {
        $this->factured = $factured;

        return $this;
    }

    /**
     * Get factured
     *
     * @return string
     */
    public function getFactured()
    {
        return $this->factured;
    }

    /**
     * Set payed
     *
     * @param string $payed
     *
     * @return CreanceSearch
     */
    public function setPayed($payed)
    {
        $this->payed = $payed;

        return $this;
    }

    /**
     * Get payed
     *
     * @return string
     */
    public function getPayed()
    {
        return $this->payed;
    }

}

