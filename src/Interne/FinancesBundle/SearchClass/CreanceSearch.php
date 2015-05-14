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
     * @var integer
     *
     */
    private $idFacture;

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
     * @param float $idFacture
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
     * @return float
     */
    public function getIdFacture()
    {
        return $this->idFacture;
    }

}

