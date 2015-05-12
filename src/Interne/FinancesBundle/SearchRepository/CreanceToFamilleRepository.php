<?php

namespace Interne\FinancesBundle\SearchRepository;

use Interne\FinancesBundle\SearchClass\CreanceSearch;
use FOS\ElasticaBundle\Repository;


class CreanceToFamilleRepository extends CreanceRepository
{

    public function search(CreanceSearch $creanceSearch){

        $query = parent::search($creanceSearch);

        return $this->find($query);
    }
}