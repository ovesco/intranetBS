<?php

namespace Interne\FinancesBundle\SearchClass;

/**
 * Class PayementSearch
 * @package Interne\FinancesBundle\SearchClass
 */
class PayementSearch
{
    
    /**
     * @var \DateTime
     *
     */
    private $toDate;

    /**
     * @var \DateTime
     *
     */
    private $fromDate;

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
     * @var string
     */
    private $state;


    /**
     * Set state
     *
     * @param string $state
     *
     * @return PayementSearch
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }


    /**
     * Set toDate
     *
     * @param \DateTime $toDate
     *
     * @return PayementSearch
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;

        return $this;
    }

    /**
     * Get toDate
     *
     * @return \DateTime
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     * Set fromDate
     *
     * @param \DateTime $fromDate
     *
     * @return PayementSearch
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    /**
     * Get fromDate
     *
     * @return \DateTime
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }


    /**
     * Set toMontantRecu
     *
     * @param float $toMontantRecu
     *
     * @return PayementSearch
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
     * @return PayementSearch
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
     * @return PayementSearch
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



}

