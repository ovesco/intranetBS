<?php

namespace Interne\FinancesBundle\SearchRepository;


use Interne\FinancesBundle\SearchClass\CreanceSearch;
use FOS\ElasticaBundle\Repository;


class CreanceRepository extends Repository
{

    public function search(CreanceSearch $creanceSearch){




        if ($creanceSearch->getTitre() != null && $creanceSearch != '') {
            $query = new \Elastica\Query\Match();
            $query->setFieldQuery('creance.titre', $creanceSearch->getTitre());
            //
        } else {
            $query = new \Elastica\Query\MatchAll();
        }
        $baseQuery = $query;



        // then we create filters depending on the chosen criterias
        $boolFilter = new \Elastica\Filter\Bool();

        /*
            Dates filter
            We add this filter only the getIspublished filter is not at "false"
        */
        if( null !== $creanceSearch->getFromDateCreation() && null !== $creanceSearch->getToDateCreation())
        {
            $boolFilter->addMust(new \Elastica\Filter\Range('CreatedAt',
                array(
                    'gte' => \Elastica\Util::convertDate($creanceSearch->getFromDateCreation()->getTimestamp()),
                    'lte' => \Elastica\Util::convertDate($creanceSearch->getToDateCreation()->getTimestamp())
                )
            ));
        }



        $filtered = new \Elastica\Query\Filtered($baseQuery, $boolFilter);

        $finalQuery = \Elastica\Query::create($filtered);



        return $finalQuery;



    }

}
