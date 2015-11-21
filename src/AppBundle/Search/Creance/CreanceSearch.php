<?php

namespace AppBundle\Search\Creance;

use AppBundle\Search\NumericIntervalSearch;
use AppBundle\Search\DateIntervalSearch;


class CreanceSearch
{
    /**
     * @var integer
     *
     */
    public $id;

    /**
     * @var string
     *
     */
    public $titre;

    /**
     * @var string
     *
     */
    public $remarque;

    /**
     * @var DateIntervalSearch
     *
     */
    public $intervalDateCreation;

    /**
     * @var DateIntervalSearch
     *
     */
    public $intervalDatePayement;


    /**
     * @var NumericIntervalSearch
     */
    public $intervalMontantEmis;

    /**
     * @var NumericIntervalSearch
     *
     */
    public $intervalMontantRecu;

    /**
     * @var boolean
     */
    public $isFactured;

    /**
     * @var boolean
     */
    public $isPayed;

    /**
     * @var string
     */
    public $debiteur;

    public function __construct(){
        $this->intervalMontantEmis = new NumericIntervalSearch();
        $this->intervalMontantRecu = new NumericIntervalSearch();
        $this->intervalDatePayement = new DateIntervalSearch();
        $this->intervalDateCreation = new DateIntervalSearch();
    }


}

