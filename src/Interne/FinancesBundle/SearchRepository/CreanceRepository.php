<?php

namespace Interne\FinancesBundle\SearchRepository;


use Interne\FinancesBundle\SearchClass\CreanceSearch;
use FOS\ElasticaBundle\Repository;


class CreanceRepository extends Repository
{

    public function search(CreanceSearch $creanceSearch){



        $query = new \Elastica\Query();


        /*
         * fixme ceci n'est pas propre mais par defaut la taille est 10...je prend un peu de marge ;-)
         */
        $query->setSize(50000);

        $boolQuery = new \Elastica\Query\Bool();


        $titre = $creanceSearch->getTitre();
        if($titre != null && $titre != '')
        {
            $fieldQuery = new \Elastica\Query\Match();
            $fieldQuery->setFieldQuery('titre',$titre);
            $fieldQuery->setFieldMinimumShouldMatch('titre','100%');
            $boolQuery->addMust($fieldQuery);
        }


        $fromMontantEmis = $creanceSearch->getFromMontantEmis();
        if($fromMontantEmis != null)
        {
            $fromMontantEmisQuery = new \Elastica\Query\Range('montantEmis',array('gte'=>$fromMontantEmis));
            $boolQuery->addMust($fromMontantEmisQuery);
        }

        $toMontantEmis = $creanceSearch->getToMontantEmis();
        if($toMontantEmis != null)
        {
            $toMontantEmisQuery = new \Elastica\Query\Range('montantEmis',array('lte'=>$toMontantEmis));
            $boolQuery->addMust($toMontantEmisQuery);
        }


        $fromMontantRecu = $creanceSearch->getFromMontantRecu();
        if($fromMontantRecu != null)
        {
            $fromMontantRecuQuery = new \Elastica\Query\Range('montantRecu',array('gte'=>$fromMontantRecu));
            $boolQuery->addMust($fromMontantRecuQuery);
        }

        $toMontantRecu = $creanceSearch->getToMontantRecu();
        if($toMontantRecu != null)
        {
            $toMontantRecuQuery = new \Elastica\Query\Range('montantRecu',array('lte'=>$toMontantRecu));
            $boolQuery->addMust($toMontantRecuQuery);
        }




        /*
         * fixme il y a encore un problème si on cherche le meme jours en from et to...y a aucun résultats...
         */
        $fromDateCreation = $creanceSearch->getFromDateCreation();
        if($fromDateCreation != null)
        {

            $fromDateCreationQuery = new \Elastica\Query\Range('dateCreation',array('gte'=>\Elastica\Util::convertDate($fromDateCreation->getTimestamp())));
            //$fromDateCreationQuery = new \Elastica\Query\Range('dateCreation',array('gte'=>\Elastica\Util::convertDate($fromDateCreation->getTimestamp())));
            $boolQuery->addMust($fromDateCreationQuery);
        }


        $toDateCreation = $creanceSearch->getToDateCreation();
        if($toDateCreation != null)
        {
            $toDateCreationQuery = new \Elastica\Query\Range('dateCreation',array('lte'=>\Elastica\Util::convertDate($toDateCreation->getTimestamp())));
            $boolQuery->addMust($toDateCreationQuery);
        }




        $idFacture = $creanceSearch->getIdFacture();
        if($idFacture != null && $idFacture != '')
        {

            $factureQuery = new \Elastica\Query\Bool();

            $bool = new \Elastica\Filter\Bool();
            $bool->addMust(new \Elastica\Filter\Term(['facture.id' => $idFacture]));

            $nested = new \Elastica\Filter\Nested();
            $nested->setPath("facture");
            $nested->setFilter($bool);

            $nested->setQuery($factureQuery);

            $factureQuery = new \Elastica\Query\Filtered($factureQuery, $nested);




            ///$fieldQuery = new \Elastica\Query\Match();
            //$fieldQuery->setFieldQuery('facture.id',$idFacture);
            //$fieldQuery->setFieldMinimumShouldMatch('facture.id','100%');
            $boolQuery->addMust($factureQuery);
        }







        $query->setQuery($boolQuery);

        return $query;



    }

}

/*
           Dates filter
           We add this filter only the getIspublished filter is not at "false"
       *
       if( null !== $creanceSearch->getFromDateCreation() && null !== $creanceSearch->getToDateCreation())
       {
           $boolFilter->addMust(new \Elastica\Filter\Range('CreatedAt',
               array(
                   'gte' => \Elastica\Util::convertDate($creanceSearch->getFromDateCreation()->getTimestamp()),
                   'lte' => \Elastica\Util::convertDate($creanceSearch->getToDateCreation()->getTimestamp())
               )
           ));
       }
       */


/*

$filtered = new \Elastica\Query\Filtered($baseQuery, $boolFilter);

$finalQuery = \Elastica\Query::create($filtered);



*/

//$baseQuery = new \Elastica\Query\MatchAll();

/*
if ($creanceSearch->getTitre() != null && $creanceSearch != '') {
    $query = new \Elastica\Query\Match();
    $query->setFieldQuery('creance.titre', $creanceSearch->getTitre());
    //
} else {
    $query = new \Elastica\Query\MatchAll();
}
$baseQuery = $query;


*/


/*
            new \Elastica\Query\Term();

            $queryString = new \Elastica\Query\QueryString();
            $queryString->setQuery($creanceSearch->getTitre());
            $queryString->setAnalyzer('classic_analyser');
            $queryString->setFields(array('creance_to_membre.titre'));
            $boolFilter->addMust($queryString);
           // $query->setQuery($boolQuery);

            */



/*
$tagsQuery = new \Elastica\Query\Terms();
$tagsQuery->setTerms('tags', array('tag1', 'tag2'));
$boolQuery->addShould($tagsQuery);

$categoryQuery = new \Elastica\Query\Terms();
$categoryQuery->setTerms('categoryIds', array('1', '2', '3'));
$boolQuery->addMust($categoryQuery);

*/



/*
        $boolFilter= new \Elastica\Filter\Bool();


        $termFilter = new \Elastica\Filter\Term();

        $termFilter->setParam('titre','Cotisation 2008');


        $boolFilter->addMust($termFilter);


        $boolQuery = new \Elastica\Filter\Bool();

        $stringQuery = new Query\QueryString();

        $stringQuery->setFields(array('titre'));
        $stringQuery->setQuery($creanceSearch->getTitre());

        $boolQuery->addMust($stringQuery);


        $remarqueQuery = new Query\QueryString();

        $remarqueQuery->setFields(array('remarque'));
        $remarqueQuery->setQuery($creanceSearch->getRemarque());

        $boolQuery->addMust($remarqueQuery);


        $filtered = new \Elastica\Query\Filtered($finalQuery, $boolQuery);

        $finalQuery = \Elastica\Query::create($filtered);

        //$finalQuery->setQuery($boolQuery);



        //$boolQuery->addMust($stringQuery);

        //$finalQuery->setQuery($boolQuery);

        */

/*
$filteredQuery = new \Elastica\Query\Filtered($query, $boolFilter);

$query->setQuery($filteredQuery);

*/

//$query->setPostFilter($boolFilter);