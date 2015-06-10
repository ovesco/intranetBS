<?php

namespace Interne\FinancesBundle\SearchRepository;


use Interne\FinancesBundle\SearchClass\PayementSearch;
use FOS\ElasticaBundle\Repository;


class PayementRepository extends Repository
{

    public function search(PayementSearch $payementSearch){

        $query = new \Elastica\Query();

        /*
         * fixme ceci n'est pas propre mais par defaut la taille est 10...je prend un peu de marge ;-)
         */
        //$query->setSize(50000);

        $emptyQuery = true;


        $boolQuery = new \Elastica\Query\Bool();


        $state = $payementSearch->getState();
        if($state != null && $state != '')
        {
            $emptyQuery = false;

            $fieldQuery = new \Elastica\Query\Match();
            $fieldQuery->setFieldQuery('state',$state);
            $fieldQuery->setFieldMinimumShouldMatch('$state','100%');
            $boolQuery->addMust($fieldQuery);
        }

        /*
         * Recherche par ID de facture
         */
        $id = $payementSearch->getIdFacture();
        if(($id != null) && ($id != '')){

            $emptyQuery = false;
            $termQuery = new \Elastica\Query\Term(array('idFacture'=>$id));
            $boolQuery->addMust($termQuery);

        }

        $fromMontantRecu = $payementSearch->getFromMontantRecu();
        if($fromMontantRecu != null)
        {
            $emptyQuery = false;

            $fromMontantRecuQuery = new \Elastica\Query\Range('montantRecu',array('gte'=>$fromMontantRecu));
            $boolQuery->addMust($fromMontantRecuQuery);
        }

        $toMontantRecu = $payementSearch->getToMontantRecu();
        if($toMontantRecu != null)
        {
            $emptyQuery = false;

            $toMontantRecuQuery = new \Elastica\Query\Range('montantRecu',array('lte'=>$toMontantRecu));
            $boolQuery->addMust($toMontantRecuQuery);
        }

        /*
         * fixme il y a encore un problème si on cherche le meme jours en from et to...y a aucun résultats...
         */
        $fromDate = $payementSearch->getFromDate();
        if($fromDate != null)
        {
            $emptyQuery = false;

            $fromDateQuery = new \Elastica\Query\Range('date',array('gte'=>\Elastica\Util::convertDate($fromDate->getTimestamp())));
            //$fromDateCreationQuery = new \Elastica\Query\Range('dateCreation',array('gte'=>\Elastica\Util::convertDate($fromDateCreation->getTimestamp())));
            $boolQuery->addMust($fromDateQuery);
        }


        $toDate = $payementSearch->getToDate();
        if($toDate != null)
        {
            $emptyQuery = false;

            $toDateQuery = new \Elastica\Query\Range('date',array('lte'=>\Elastica\Util::convertDate($toDate->getTimestamp())));
            $boolQuery->addMust($toDateQuery);
        }

        $query->setQuery($boolQuery);


        if(!$emptyQuery){
            return $this->find($query);
        }
        else
        {
            return array();
        }

    }

}
