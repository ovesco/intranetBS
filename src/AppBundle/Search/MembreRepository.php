<?php

namespace AppBundle\Search;

use FOS\ElasticaBundle\Repository;
use AppBundle\Utils\Elastic\QueryBuilder;

class MembreRepository extends Repository
{

    public function search(MembreSearch $membre){

        $builder = new QueryBuilder($this,true,5000);

        $builder
            ->addTextMatch('prenom',$membre->prenom)
            ->addNestedTextMatch('famille.nom',$membre->nom)
            ->addDateGreaterOrEqual('naissance',$membre->fromNaissance)
            ->addDateLessOrEqual('naissance',$membre->toNaissance)
            ->addTextMatch('sexe',$membre->sexe)
            ->addNestedTextMatch('attributions.groupe',$membre->attribution->groupe)
            ->addNestedTextMatch('attributions.fonction',$membre->attribution->fonction)
        ;

        return $builder->getResults();

    }

}
