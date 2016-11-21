<?php

namespace AppBundle\Search\Payement;


use AppBundle\Search\Payement\PayementSearch;
use FOS\ElasticaBundle\Repository;
use AppBundle\Utils\Elastic\QueryBuilder;

class PayementRepository extends Repository
{

    /**
     * @param PayementSearch $payement
     * @return array
     *
     */
    public function search(PayementSearch $payement){

        $builder = new QueryBuilder($this,true,5000);

        $builder
            ->addNumber('idFacture',$payement->idFacture)
            ->addNumberInRange('montantRecu',$payement->intervalMontantRecu->lower,$payement->intervalMontantRecu->higher)
            ->addTextMatch('remarques',$payement->remarque)
            ->addTextMatch('state',$payement->state)
            ->addNumberInRange('idFacture',$payement->intervalIdFacture->lower,$payement->intervalIdFacture->higher)
            ->addDateInRange('date',$payement->intervalDate->lower,$payement->intervalDate->higher)
            ->addBoolean('validated',$payement->validated)
        ;

        return $builder->getResults();
    }

}
