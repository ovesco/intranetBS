<?php

namespace Interne\FinancesBundle\SearchClass;

/**
 * FactureSearch
 *
 */
class FactureSearch
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
    private $statut;


    /**
     * @var integer
     *
     */
    private $nombreRappels;

    /**
     * @var float
     *
     */
    private $fromMontantEmis;

    /**
     * @var float
     *
     */
    private $toMontantEmis;

    /**
     * @var float
     *
     */
    private $fromMontantRecu;

    /**
     * @var float
     *
     */
    private $toMontantRecu;

    /**
     * @var float
     *
     * Montant total des créances de la facture
     *
     */
    private $fromMontantEmisCreances;

    /**
     * @var float
     *
     * Montant total des créances de la facture
     *
     */
    private $toMontantEmisCreances;

    /**
     * @var float
     *
     * Montant total des rappels de la facture
     *
     */
    private $fromMontantEmisRappels;

    /**
     * @var float
     *
     * Montant total des rappels de la facture
     *
     */
    private $toMontantEmisRappels;

    /**
     * @var \DateTime
     *
     */
    private $fromDateCreationRappel;

    /**
     * @var \DateTime
     *
     */
    private $toDateCreationRappel;

    /**
     * @var \DateTime
     *
     */
    private $fromDateCreation;

    /**
     * @var \DateTime
     *
     */
    private $toDateCreation;

    /**
     * @var \DateTime
     *
     */
    private $fromDateCreationCreance;

    /**
     * @var \DateTime
     *
     */
    private $toDateCreationCreance;

    /**
     * @var \DateTime
     *
     */
    private $fromDatePayement;

    /**
     * @var \DateTime
     *
     */
    private $toDatePayement;

    /**
     * @var string
     *
     */
    private $titreCreance;

    /**
     * @var float
     *
     * montant d'une créance
     *
     */
    private $fromMontantEmisCreance;

    /**
     * @var float
     *
     * montant d'une créance
     *
     */
    private $toMontantEmisCreance;

    /**
     * @var float
     *
     * montant d'un rappel
     *
     */
    private $fromMontantEmisRappel;

    /**
     * @var float
     *
     * montant d'un rappel
     *
     */
    private $toMontantEmisRappel;

    /**
     * Set id
     *
     * @param integer $id
     * @return FactureSearch
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
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
     * Set statut
     *
     * @param string $statut
     *
     * @return FactureSearch
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
     * Set nombreRappels
     *
     * @param integer $nombreRappels
     *
     * @return FactureSearch
     */
    public function setNombreRappels($nombreRappels)
    {
        $this->nombreRappels = $nombreRappels;

        return $this;
    }

    /**
     * Get nombreRappels
     *
     * @return integer
     */
    public function getNombreRappels()
    {
        return $this->nombreRappels;
    }

    /**
     * Set fromMontantEmis
     *
     * @param float $fromMontantEmis
     *
     * @return FactureSearch
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
     * Set toMontantEmis
     *
     * @param float $toMontantEmis
     *
     * @return FactureSearch
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
     * Set fromMontantRecu
     *
     * @param float $fromMontantRecu
     *
     * @return FactureSearch
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
     * Set toMontantRecu
     *
     * @param float $toMontantRecu
     *
     * @return FactureSearch
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
     * Set fromMontantEmisCreances
     *
     * @param float $fromMontantEmisCreances
     *
     * @return FactureSearch
     */
    public function setFromMontantEmisCreances($fromMontantEmisCreances)
    {
        $this->fromMontantEmisCreances = $fromMontantEmisCreances;

        return $this;
    }

    /**
     * Get fromMontantEmisCreances
     *
     * @return float
     */
    public function getFromMontantEmisCreances()
    {
        return $this->fromMontantEmisCreances;
    }

    /**
     * Set toMontantEmisCreances
     *
     * @param float $toMontantEmisCreances
     *
     * @return FactureSearch
     */
    public function setToMontantEmisCreances($toMontantEmisCreances)
    {
        $this->toMontantEmisCreances = $toMontantEmisCreances;

        return $this;
    }

    /**
     * Get toMontantEmisCreances
     *
     * @return float
     */
    public function getToMontantEmisCreances()
    {
        return $this->toMontantEmisCreances;
    }

    /**
     * Set fromMontantEmisRappels
     *
     * @param float $fromMontantEmisRappels
     *
     * @return FactureSearch
     */
    public function setFromMontantEmisRappels($fromMontantEmisRappels)
    {
        $this->fromMontantEmisRappels = $fromMontantEmisRappels;

        return $this;
    }

    /**
     * Get fromMontantEmisRappels
     *
     * @return float
     */
    public function getFromMontantEmisRappels()
    {
        return $this->fromMontantEmisRappels;
    }

    /**
     * Set toMontantEmisRappels
     *
     * @param float $toMontantEmisRappels
     *
     * @return FactureSearch
     */
    public function setToMontantEmisRappels($toMontantEmisRappels)
    {
        $this->toMontantEmisRappels = $toMontantEmisRappels;

        return $this;
    }

    /**
     * Get toMontantEmisRappels
     *
     * @return float
     */
    public function getToMontantEmisRappels()
    {
        return $this->toMontantEmisRappels;
    }

    /**
     * Set fromDateCreationRappel
     *
     * @param \DateTime $fromDateCreationRappel
     *
     * @return FactureSearch
     */
    public function setFromDateCreationRappel($fromDateCreationRappel)
    {
        $this->fromDateCreationRappel = $fromDateCreationRappel;

        return $this;
    }

    /**
     * Get fromDateCreationRappel
     *
     * @return \DateTime
     */
    public function getFromDateCreationRappel()
    {
        return $this->fromDateCreationRappel;
    }

    /**
     * Set toDateCreationRappel
     *
     * @param \DateTime $toDateCreationRappel
     *
     * @return FactureSearch
     */
    public function setToDateCreationRappel($toDateCreationRappel)
    {
        $this->toDateCreationRappel = $toDateCreationRappel;

        return $this;
    }

    /**
     * Get toDateCreationRappel
     *
     * @return \DateTime
     */
    public function getToDateCreationRappel()
    {
        return $this->toDateCreationRappel;
    }

    /**
     * Set fromDateCreation
     *
     * @param \DateTime $fromDateCreation
     *
     * @return FactureSearch
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
     * Set toDateCreation
     *
     * @param \DateTime $toDateCreation
     *
     * @return FactureSearch
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
     * Set fromDateCreationCreance
     *
     * @param \DateTime $fromDateCreationCreance
     *
     * @return FactureSearch
     */
    public function setFromDateCreationCreance($fromDateCreationCreance)
    {
        $this->fromDateCreationCreance = $fromDateCreationCreance;

        return $this;
    }

    /**
     * Get fromDateCreationCreance
     *
     * @return \DateTime
     */
    public function getFromDateCreationCreance()
    {
        return $this->fromDateCreationCreance;
    }

    /**
     * Set toDateCreationCreance
     *
     * @param \DateTime $toDateCreationCreance
     *
     * @return FactureSearch
     */
    public function setToDateCreationCreance($toDateCreationCreance)
    {
        $this->toDateCreationCreance = $toDateCreationCreance;

        return $this;
    }

    /**
     * Get toDateCreationCreance
     *
     * @return \DateTime
     */
    public function getToDateCreationCreance()
    {
        return $this->toDateCreationCreance;
    }

    /**
     * Set fromDatePayement
     *
     * @param \DateTime $fromDatePayement
     *
     * @return FactureSearch
     */
    public function setFromDatePayement($fromDatePayement)
    {
        $this->fromDatePayement = $fromDatePayement;

        return $this;
    }

    /**
     * Get fromDatePayement
     *
     * @return \DateTime
     */
    public function getFromDatePayement()
    {
        return $this->fromDatePayement;
    }

    /**
     * Set toDatePayement
     *
     * @param \DateTime $toDatePayement
     *
     * @return FactureSearch
     */
    public function setToDatePayement($toDatePayement)
    {
        $this->toDatePayement = $toDatePayement;

        return $this;
    }

    /**
     * Get toDatePayement
     *
     * @return \DateTime
     */
    public function getToDatePayement()
    {
        return $this->toDatePayement;
    }

    /**
     * Set titreCreance
     *
     * @param string $titreCreance
     *
     * @return FactureSearch
     */
    public function setTitreCreance($titreCreance)
    {
        $this->titreCreance = $titreCreance;

        return $this;
    }

    /**
     * Get titreCreance
     *
     * @return string
     */
    public function getTitreCreance()
    {
        return $this->titreCreance;
    }

    /**
     * Set fromMontantEmisCreance
     *
     * @param float $fromMontantEmisCreance
     *
     * @return FactureSearch
     */
    public function setFromMontantEmisCreance($fromMontantEmisCreance)
    {
        $this->fromMontantEmisCreance = $fromMontantEmisCreance;

        return $this;
    }

    /**
     * Get fromMontantEmisCreance
     *
     * @return float
     */
    public function getFromMontantEmisCreance()
    {
        return $this->fromMontantEmisCreance;
    }

    /**
     * Set toMontantEmisCreance
     *
     * @param float $toMontantEmisCreance
     *
     * @return FactureSearch
     */
    public function setToMontantEmisCreance($toMontantEmisCreance)
    {
        $this->toMontantEmisCreance = $toMontantEmisCreance;

        return $this;
    }

    /**
     * Get toMontantEmisCreance
     *
     * @return float
     */
    public function getToMontantEmisCreance()
    {
        return $this->toMontantEmisCreance;
    }


    /**
     * Set fromMontantEmisRappel
     *
     * @param float $fromMontantEmisRappel
     *
     * @return FactureSearch
     */
    public function setFromMontantEmisRappel($fromMontantEmisRappel)
    {
        $this->fromMontantEmisRappel = $fromMontantEmisRappel;

        return $this;
    }

    /**
     * Get fromMontantEmisRappel
     *
     * @return float
     */
    public function getFromMontantEmisRappel()
    {
        return $this->fromMontantEmisRappel;
    }

    /**
     * Set toMontantEmisRappel
     *
     * @param float $toMontantEmisRappel
     *
     * @return FactureSearch
     */
    public function setToMontantEmisRappel($toMontantEmisRappel)
    {
        $this->toMontantEmisRappel= $toMontantEmisRappel;

        return $this;
    }

    /**
     * Get toMontantEmisRappel
     *
     * @return float
     */
    public function getToMontantEmisRappel()
    {
        return $this->toMontantEmisRappel;
    }
}

