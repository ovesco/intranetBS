<?php

namespace Interne\FinancesBundle\SearchRepository;

use FOS\ElasticaBundle\Repository;
use Interne\FinancesBundle\SearchClass\FactureSearch;

class FactureRepository extends Repository
{
    public function search(FactureSearch $factureSearch){


        $query = new \Elastica\Query();

        /*
         * fixme ceci n'est pas propre mais par defaut la taille est 10...je prend un peu de marge ;-)
         */
        $query->setSize(50000);

        $emptyQuery = true;


        $boolQuery = new \Elastica\Query\Bool();


        /*
         * Recherche par ID de facture
         */
        $id = $factureSearch->getId();
        if(($id != null) && ($id != '')){

            $emptyQuery = false;
            $termQuery = new \Elastica\Query\Term(array('id'=>$id));
            $boolQuery->addMust($termQuery);

        }

        /*
         * Recherche par le montant emis (=montant total)
         */
        $fromMontantEmis = $factureSearch->getFromMontantEmis();
        if($fromMontantEmis != null)
        {
            $emptyQuery = false;

            $fromMontantEmisQuery = new \Elastica\Query\Range('montantEmis',array('gte'=>$fromMontantEmis));
            $boolQuery->addMust($fromMontantEmisQuery);
        }

        $toMontantEmis = $factureSearch->getToMontantEmis();
        if($toMontantEmis != null)
        {
            $emptyQuery = false;

            $toMontantEmisQuery = new \Elastica\Query\Range('montantEmis',array('lte'=>$toMontantEmis));
            $boolQuery->addMust($toMontantEmisQuery);
        }

        /*
         * Recherche par le montant emis par les créances
         */
        $fromMontantEmisCreances = $factureSearch->getFromMontantEmisCreances();
        if($fromMontantEmisCreances != null)
        {
            $emptyQuery = false;

            $fromMontantEmisQuery = new \Elastica\Query\Range('montantEmisCreances',array('gte'=>$fromMontantEmisCreances));
            $boolQuery->addMust($fromMontantEmisQuery);
        }

        $toMontantEmisCreances = $factureSearch->getToMontantEmisCreances();
        if($toMontantEmisCreances != null)
        {
            $emptyQuery = false;

            $toMontantEmisQuery = new \Elastica\Query\Range('montantEmisCreances',array('lte'=>$toMontantEmisCreances));
            $boolQuery->addMust($toMontantEmisQuery);
        }

        /*
         * Recherche par le montant emis par les rappels
         */
        $fromMontantEmisRappels = $factureSearch->getFromMontantEmisRappels();
        if($fromMontantEmisRappels!= null)
        {
            $emptyQuery = false;

            $fromMontantEmisQuery = new \Elastica\Query\Range('montantEmisRappels',array('gte'=>$fromMontantEmisRappels));
            $boolQuery->addMust($fromMontantEmisQuery);
        }

        $toMontantEmisRappels = $factureSearch->getToMontantEmisRappels();
        if($toMontantEmisRappels != null)
        {
            $emptyQuery = false;

            $toMontantEmisQuery = new \Elastica\Query\Range('montantEmisRappels',array('lte'=>$toMontantEmisRappels));
            $boolQuery->addMust($toMontantEmisQuery);
        }


        /*
         * fixme il y a encore un problème si on cherche le meme jours en from et to...y a aucun résultats...
         */
        $fromDateCreation = $factureSearch->getFromDateCreation();
        if($fromDateCreation != null)
        {
            $emptyQuery = false;

            $fromDateCreationQuery = new \Elastica\Query\Range('dateCreation',array('gte'=>\Elastica\Util::convertDate($fromDateCreation->getTimestamp())));
            //$fromDateCreationQuery = new \Elastica\Query\Range('dateCreation',array('gte'=>\Elastica\Util::convertDate($fromDateCreation->getTimestamp())));
            $boolQuery->addMust($fromDateCreationQuery);
        }


        $toDateCreation = $factureSearch->getToDateCreation();
        if($toDateCreation != null)
        {
            $emptyQuery = false;

            $toDateCreationQuery = new \Elastica\Query\Range('dateCreation',array('lte'=>\Elastica\Util::convertDate($toDateCreation->getTimestamp())));
            $boolQuery->addMust($toDateCreationQuery);
        }



        /*
         * Recherche sur les propriétés d'une créance associée à la facture
         */

        $titre = $factureSearch->getTitreCreance();
        if($titre != null && $titre != '')
        {
            $emptyQuery = false;





            $baseQuery = new \Elastica\Query\MatchAll();

            $term = new \Elastica\Filter\Term(array('creances.titre' => $titre));

            $boolFilter = new \Elastica\Filter\Bool();
            $boolFilter->addMust($term);

            $nested = new \Elastica\Filter\Nested();
            $nested->setPath('creances');
            $nested->setFilter($boolFilter);


            $titreCreanceQuery = new \Elastica\Query\Filtered($baseQuery, $nested);

            var_dump($titreCreanceQuery->toArray());

            $boolQuery->addMust($titreCreanceQuery);


        }


        $fromMontantEmisCreance = $factureSearch->getFromMontantEmisCreance();
        $toMontantEmisCreance = $factureSearch->getToMontantEmisCreance();

        $fromIsSet = ($fromMontantEmisCreance != null) && ($fromMontantEmisCreance != '');
        $toIsSet = ($toMontantEmisCreance != null) && ($toMontantEmisCreance != '');
        if($fromIsSet || $toIsSet)
        {
            $emptyQuery = false;

            $baseQuery = new \Elastica\Query\MatchAll();

            $term = null;

            if($fromIsSet && $toIsSet){
                $term = new \Elastica\Filter\Range('creances.montantEmis',array(
                    'gte'=>$fromMontantEmisCreance,
                    'lte'=>$toMontantEmisCreance
                ));

            }
            elseif($fromIsSet){
                $term = new \Elastica\Filter\Range('creances.montantEmis',array(
                    'gte'=>$fromMontantEmisCreance
                ));
            }
            else{
                $term = new \Elastica\Filter\Range('creances.montantEmis',array(
                    'lte'=>$toMontantEmisCreance
                ));

            }


            $boolFilter = new \Elastica\Filter\Bool();
            $boolFilter->addMust($term);

            $nested = new \Elastica\Filter\Nested();
            $nested->setPath('creances');
            $nested->setFilter($boolFilter);


            $fromMontantEmisCreanceQuery = new \Elastica\Query\Filtered($baseQuery, $nested);

            var_dump($fromMontantEmisCreanceQuery->toArray());

            $boolQuery->addMust($fromMontantEmisCreanceQuery);


        }



        $nombreRappels = $factureSearch->getNombreRappels();
        if(($nombreRappels != null) && ($nombreRappels != '')){

            $emptyQuery = false;
            $termQuery = new \Elastica\Query\Term(array('nombreRappels'=>$nombreRappels));
            $boolQuery->addMust($termQuery);


        }


        return array('mainQuery'=>$query,'boolQuery'=>$boolQuery,'emptyQuery'=>$emptyQuery);


    }

}
