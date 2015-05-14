<?php

namespace Interne\FinancesBundle\SearchRepository;


use Interne\FinancesBundle\SearchClass\CreanceSearch;
use FOS\ElasticaBundle\Repository;
use Elastica\Query;


class CreanceToMembreRepository extends CreanceRepository
{

    public function search(CreanceSearch $creanceSearch){


        $arrayOfQuery = parent::search($creanceSearch);

        /** @var \Elastica\Query $query */
        $query = $arrayOfQuery['mainQuery'];
        /** @var \Elastica\Query\Bool $boolQuery */
        $boolQuery = $arrayOfQuery['boolQuery'];

        /** @var boolean $emptyQuery */
        $emptyQuery = $arrayOfQuery['emptyQuery'];


        $prenom = $creanceSearch->getPrenomMembre();
        if($prenom != null && $prenom != '')
        {
            $emptyQuery = false;

            /*
             * C'est nécéssaire car elastica parse en minuscule
             */

            $prenom = strtolower($prenom);

            $baseQuery = new \Elastica\Query\MatchAll();


            $term = new \Elastica\Filter\Term(array('membre.prenom' => $prenom));

            $boolFilter = new \Elastica\Filter\Bool();
            $boolFilter->addMust($term);

            $nested = new \Elastica\Filter\Nested();
            $nested->setPath("membre");
            $nested->setFilter($boolFilter);


            $prenomQuery = new \Elastica\Query\Filtered($baseQuery, $nested);



            $boolQuery->addMust($prenomQuery);
        }



        $nom = $creanceSearch->getNomMembre();
        if($nom != null && $nom != '')
        {

            $emptyQuery = false;
            /*
             * C'est nécéssaire car elastica parse en minuscule
             */

            $nom = strtolower($nom);

            $baseQuery = new \Elastica\Query\MatchAll();


            $term = new \Elastica\Filter\Term(array('membre.nom' => $nom));

            $boolFilter = new \Elastica\Filter\Bool();
            $boolFilter->addMust($term);

            $nested = new \Elastica\Filter\Nested();
            $nested->setPath("membre");
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