<?php

namespace Interne\FinancesBundle\Search;


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
     * @var \DateTime
     *
     */
    public $toDateCreation;

    /**
     * @var \DateTime
     *
     */
    public $fromDateCreation;

    /**
     * @var float
     *
     */
    public $toMontantEmis;

    /**
     * @var float
     *
     */
    public $fromMontantEmis;

    /**
     * @var float
     *
     */
    public $toMontantRecu;

    /**
     * @var float
     *
     */
    public $fromMontantRecu;

}

