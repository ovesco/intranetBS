<?php

namespace Interne\FinancesBundle\Search;
use AppBundle\Search\DateIntervalSearch;
use AppBundle\Search\NumericIntervalSearch;

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
    public $id;

    /**
     * @var NumericIntervalSearch
     */
    public $intervalMontantEmis;

    /**
     * @var NumericIntervalSearch
     */
    public $intervalMontantRecu;

    /**
     * @var DateIntervalSearch
     */
    public $intervalDateCreation;

    /**
     * @var DateIntervalSearch
     */
    public $intervalDatePayement;

    /**
     * @var string
     */
    public $statut;

    /**
     * @var integer
     */
    public $nombreRappels;

    /**
     * @var string
     */
    public $debiteur;

    /**
     * @var string
     */
    public $titreCreance;

    public function __construct()
    {
        $this->intervalMontantEmis = new NumericIntervalSearch();
        $this->intervalMontantRecu = new NumericIntervalSearch();
        $this->intervalDateCreation = new DateIntervalSearch();
        $this->intervalDatePayement = new DateIntervalSearch();
    }


}

