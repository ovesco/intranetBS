<?php

namespace AppBundle\Search\Famille;

use FOS\ElasticaBundle\Repository;
use AppBundle\Utils\Elastic\QueryBuilder;

class FamilleRepository extends Repository
{

    public function search(FamilleSearch $famille){

        $builder = new QueryBuilder($this,true,5000);

        $builder
            ->addTextMatch('nom',$famille->nom)
        ;

        return $builder->getResults();

    }

}
