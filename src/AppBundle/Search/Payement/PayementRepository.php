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
     * todo NUR a faire.
     */
    public function search(PayementSearch $payement){

        $builder = new QueryBuilder($this,true,5000);

        $builder
            ->addNumberInRange('montantRecu',$payement->intervalMontantRecu->lower,$payement->intervalMontantRecu->higher)
           // ->addTextMatch('titre',$creance->titre)
//            ->addTextMatch('remarque',$creance->remarque)
//            ->addNumberInRange('montantEmis',$creance->intervalMontantEmis->lower,$creance->intervalMontantEmis->higher)
//            ->addNumberInRange('montantRecu',$creance->intervalMontantRecu->lower,$creance->intervalMontantRecu->higher)
//            ->addDateInRange('dateCreation',$creance->intervalDateCreation->lower,$creance->intervalDateCreation->higher)
//            ->addDateInRange('datePayement',$creance->intervalDatePayement->lower,$creance->intervalDatePayement->higher)
//            ->addBoolean('isFactured',$creance->isFactured)
//            ->addBoolean('isPayed',$creance->isPayed)
//            ->addNestedTextMatch('debiteur.getOwnerAsString',$creance->debiteur)
        ;

        return $builder->getResults();
    }

}
