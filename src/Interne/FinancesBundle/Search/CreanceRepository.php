<?php

namespace Interne\FinancesBundle\Search;

use FOS\ElasticaBundle\Repository;
use AppBundle\Utils\Elastic\QueryBuilder;

class CreanceRepository extends Repository
{

    public function search(CreanceSearch $creance){



        $builder = new QueryBuilder($this,true,5000);

        $builder
            ->addTextMatch('titre',$creance->titre)
            ->addNumberInRange('montantEmis',$creance->fromMontantEmis,$creance->toMontantEmis)
        /*
            ->addNestedTextMatch('montantEmis',$membre->nom)
            ->addDateGreaterOrEqual('naissance',$membre->fromNaissance)
            ->addDateLessOrEqual('naissance',$membre->toNaissance)
            ->addTextMatch('sexe',$membre->sexe)
            ->addNestedTextMatch('attributions.groupe',$membre->attribution->groupe)
            ->addNestedTextMatch('attributions.fonction',$membre->attribution->fonction)
        */
        ;

        return $builder->getResults();


        /*
        $query = new \Elastica\Query();

        /*
         * fixme ceci n'est pas propre mais par defaut la taille est 10...je prend un peu de marge ;-)
         *
        $query->setSize(50000);

        $emptyQuery = true;


        $boolQuery = new \Elastica\Query\Bool();


        $titre = $creanceSearch->getTitre();
        if($titre != null && $titre != '')
        {
            $emptyQuery = false;

            $fieldQuery = new \Elastica\Query\Match();
            $fieldQuery->setFieldQuery('titre',$titre);
            $fieldQuery->setFieldMinimumShouldMatch('titre','100%');
            $boolQuery->addMust($fieldQuery);
        }

        $remarque = $creanceSearch->getRemarque();
        if($remarque != null && $remarque != '')
        {
            $emptyQuery = false;

            $fieldQuery = new \Elastica\Query\Match();
            $fieldQuery->setFieldQuery('remarque',$remarque);
            $fieldQuery->setFieldMinimumShouldMatch('remarque','100%');
            $boolQuery->addMust($fieldQuery);
        }


        $fromMontantEmis = $creanceSearch->getFromMontantEmis();
        if($fromMontantEmis != null)
        {
            $emptyQuery = false;

            $fromMontantEmisQuery = new \Elastica\Query\Range('montantEmis',array('gte'=>$fromMontantEmis));
            $boolQuery->addMust($fromMontantEmisQuery);
        }

        $toMontantEmis = $creanceSearch->getToMontantEmis();
        if($toMontantEmis != null)
        {
            $emptyQuery = false;

            $toMontantEmisQuery = new \Elastica\Query\Range('montantEmis',array('lte'=>$toMontantEmis));
            $boolQuery->addMust($toMontantEmisQuery);
        }


        $fromMontantRecu = $creanceSearch->getFromMontantRecu();
        if($fromMontantRecu != null)
        {
            $emptyQuery = false;

            $fromMontantRecuQuery = new \Elastica\Query\Range('montantRecu',array('gte'=>$fromMontantRecu));
            $boolQuery->addMust($fromMontantRecuQuery);
        }

        $toMontantRecu = $creanceSearch->getToMontantRecu();
        if($toMontantRecu != null)
        {
            $emptyQuery = false;

            $toMontantRecuQuery = new \Elastica\Query\Range('montantRecu',array('lte'=>$toMontantRecu));
            $boolQuery->addMust($toMontantRecuQuery);
        }




        /*
         * fixme il y a encore un problème si on cherche le meme jours en from et to...y a aucun résultats...
         *
        $fromDateCreation = $creanceSearch->getFromDateCreation();
        if($fromDateCreation != null)
        {
            $emptyQuery = false;

            $fromDateCreationQuery = new \Elastica\Query\Range('dateCreation',array('gte'=>\Elastica\Util::convertDate($fromDateCreation->getTimestamp())));
            //$fromDateCreationQuery = new \Elastica\Query\Range('dateCreation',array('gte'=>\Elastica\Util::convertDate($fromDateCreation->getTimestamp())));
            $boolQuery->addMust($fromDateCreationQuery);
        }


        $toDateCreation = $creanceSearch->getToDateCreation();
        if($toDateCreation != null)
        {
            $emptyQuery = false;

            $toDateCreationQuery = new \Elastica\Query\Range('dateCreation',array('lte'=>\Elastica\Util::convertDate($toDateCreation->getTimestamp())));
            $boolQuery->addMust($toDateCreationQuery);
        }


        $factured = $creanceSearch->getFactured();
        if($factured == 'yes'){

            $emptyQuery = false;
            /*
             * si facturée, on exclu tout les facture en attente et on regarde si le num de réf est spécifié.
             *

            $termQuery = new \Elastica\Query\Term(array('isFactured'=>true));
            $boolQuery->addMust($termQuery);



            $idFacture = $creanceSearch->getIdFacture();
            if($idFacture != null && $idFacture != '')
            {
                $emptyQuery = false;

                $baseQuery = new \Elastica\Query\MatchAll();


                $term = new \Elastica\Filter\Term(array('facture.id' => $idFacture));

                $boolFilter = new \Elastica\Filter\Bool();
                $boolFilter->addMust($term);

                $nested = new \Elastica\Filter\Nested();
                $nested->setPath("facture");
                $nested->setFilter($boolFilter);


                $idFactureQuery = new \Elastica\Query\Filtered($baseQuery, $nested);

                $boolQuery->addMust($idFactureQuery);
            }



        }
        elseif($factured == 'no'){

            $emptyQuery = false;

            $termQuery = new \Elastica\Query\Term(array('isFactured'=>false));
            $boolQuery->addMust($termQuery);

        }

        $payed = $creanceSearch->getPayed();
        if($payed == 'yes'){

            $emptyQuery = false;
            $termQuery = new \Elastica\Query\Term(array('isPayed'=>true));
            $boolQuery->addMust($termQuery);


        }
        elseif($payed == 'no'){
            $emptyQuery = false;
            $termQuery = new \Elastica\Query\Term(array('isPayed'=>false));
            $boolQuery->addMust($termQuery);
        }





        return array('mainQuery'=>$query,'boolQuery'=>$boolQuery,'emptyQuery'=>$emptyQuery);

        */


    }

}
