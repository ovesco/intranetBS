<?php

namespace AppBundle\Search\Creance;

use FOS\ElasticaBundle\Repository;
use AppBundle\Utils\Elastic\QueryBuilder;

class CreanceRepository extends Repository
{


    public function search(CreanceSearch $creance){

        $builder = new QueryBuilder($this,true,5000);

        $builder
            ->addTextMatch('titre',$creance->titre)
            ->addTextMatch('remarque',$creance->remarque)
            ->addNumberInRange('montantEmis',$creance->intervalMontantEmis->lower,$creance->intervalMontantEmis->higher)
            ->addNumberInRange('montantRecu',$creance->intervalMontantRecu->lower,$creance->intervalMontantRecu->higher)
            ->addDateInRange('dateCreation',$creance->intervalDateCreation->lower,$creance->intervalDateCreation->higher)
            ->addDateInRange('datePayement',$creance->intervalDatePayement->lower,$creance->intervalDatePayement->higher)
            ->addBoolean('isFactured',$creance->isFactured)
            ->addBoolean('isPayed',$creance->isPayed)
            ->addNestedTextMatch('debiteur.getOwnerAsString',$creance->debiteur)
        ;

        return $builder->getResults();

    }

}
