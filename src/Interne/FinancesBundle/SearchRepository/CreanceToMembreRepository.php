<?php

namespace Interne\FinancesBundle\SearchRepository;


use Interne\FinancesBundle\SearchClass\CreanceSearch;
use FOS\ElasticaBundle\Repository;


class CreanceToMembreRepository extends CreanceRepository
{

    public function search(CreanceSearch $creanceSearch){


        $query = parent::search($creanceSearch);

        return $this->find($query);



    }
}