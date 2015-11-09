<?php

namespace Interne\FinancesBundle\Search;

use FOS\ElasticaBundle\Repository;
use AppBundle\Utils\Elastic\QueryBuilder;

class FactureRepository extends Repository
{
    public function search(FactureSearch $facture){

        $builder = new QueryBuilder($this,true,5000);

        $builder
            ->addNumber('id',$facture->id)
            ->addNumberInRange('montantEmis',$facture->intervalMontantEmis->lower,$facture->intervalMontantEmis->higher)
            ->addNumberInRange('montantRecu',$facture->intervalMontantRecu->lower,$facture->intervalMontantRecu->higher)
            ->addDateInRange('dateCreation',$facture->intervalDateCreation->lower,$facture->intervalDateCreation->higher)
            ->addDateInRange('datePayement',$facture->intervalDatePayement->lower,$facture->intervalDatePayement->higher)
            ->addNestedTextMatch('creances.titre',$facture->titreCreance)
            ->addTextMatch('statut',$facture->statut)
            ->addNumber('nombreRappels',$facture->nombreRappels)
            ->addNestedTextMatch('debiteur.getOwnerAsString',$facture->debiteur)

        ;



        return $builder->getResults();


    }

}
