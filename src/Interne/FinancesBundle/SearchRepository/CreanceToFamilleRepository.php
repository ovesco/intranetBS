<?php

namespace Interne\FinancesBundle\SearchRepository;

use Interne\FinancesBundle\SearchClass\CreanceSearch;
use FOS\ElasticaBundle\Repository;


class CreanceToFamilleRepository extends CreanceRepository
{

    public function search(CreanceSearch $creanceSearch){

        $arrayOfQuery = parent::search($creanceSearch);

        /** @var \Elastica\Query $query */
        $query = $arrayOfQuery['mainQuery'];
        /** @var \Elastica\Query\Bool $boolQuery */
        $boolQuery = $arrayOfQuery['boolQuery'];

        /** @var boolean $emptyQuery */
        $emptyQuery = $arrayOfQuery['emptyQuery'];


        $nom = $creanceSearch->getNomFamille();
        if($nom != null && $nom != '')
        {

            $emptyQuery = false;
            /*
             * C'est nécéssaire car elastica parse en minuscule
             */

            $nom = strtolower($nom);

            $baseQuery = new \Elastica\Query\MatchAll();


            $term = new \Elastica\Filter\Term(array('famille.nom' => $nom));

            $boolFilter = new \Elastica\Filter\Bool();
            $boolFilter->addMust($term);

            $nested = new \Elastica\Filter\Nested();
            $nested->setPath("famille");
            $nested->setFilter($boolFilter);


            $nomQuery = new \Elastica\Query\Filtered($baseQuery, $nested);



            $boolQuery->addMust($nomQuery);
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