<?php

namespace AppBundle\Search\Payement;


use AppBundle\Search\NumericIntervalSearch;
use AppBundle\Search\DateIntervalSearch;

/**
 * Class PayementSearch
 * @package AppBundle\Search
 */
class PayementSearch
{
    
    /**
     * @var DateIntervalSearch
     *
     */
    public $intervalDate;
    
    /**
     * @var NumericIntervalSearch
     *
     */
    public $intervalMontantRecu;

    /**
     * @var integer
     *
     */
    public $idFacture;

    /**
     * @var integer
     */
    public $intervalIdFacture;

    /**
     * @var string
     */
    public $state;

    /**
     * @var bool
     */
    public $validated;

    /**
     * @var string
     *
     */
    public $remarque;

    public function __construct(){
        $this->intervalMontantRecu = new NumericIntervalSearch();
        $this->intervalIdFacture = new NumericIntervalSearch();
        $this->intervalDate = new DateIntervalSearch();
    }



}

