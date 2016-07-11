<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 03.11.15
 * Time: 16:50
 */

namespace AppBundle\Utils\Elastic;

use FOS\ElasticaBundle\Repository;

class QueryBuilder {

    /**
     * @var \Elastica\Query
     */
    private $mainQuery;

    /**
     * @var \Elastica\Query\BoolQuery
     */
    private $boolQuery;

    /**
     * @var bool
     */
    private $empty;

    /**
     * @var bool
     */
    private $matchAllIfEmpty;

    /**
     * @var Repository
     */
    private $elasticRepository;

    public function __construct(Repository $elasticRepository,$matchAllIfEmpty = false,$maxResultsNumber = 10)
    {
        $this->mainQuery = new \Elastica\Query();
        $this->mainQuery->setSize($maxResultsNumber);
        $this->boolQuery = new \Elastica\Query\BoolQuery();
        $this->empty = true;
        $this->matchAllIfEmpty = $matchAllIfEmpty;
        $this->elasticRepository = $elasticRepository;
    }


    public function getResults()
    {
        if($this->empty)
        {
            if($this->matchAllIfEmpty)
            {
                $this->mainQuery->setQuery(new \Elastica\Query\MatchAll());
                return $this->elasticRepository->find($this->mainQuery);
            }
            else
            {
                return array();
            }
        }
        else
        {
            $this->mainQuery->setQuery($this->boolQuery);
            return $this->elasticRepository->find($this->mainQuery);
        }
    }


    public function addTextMatch($field,$text,$MinimumShouldMatchPercentage = "100%")
    {
        if((!is_null($text)) && ($text != ''))
        {
            $this->empty = false;
            $query = new \Elastica\Query\Match();
            $query->setFieldQuery($field,$text);
            $query->setFieldMinimumShouldMatch($field,$MinimumShouldMatchPercentage);
            $this->boolQuery->addMust($query);
        }
        return $this;
    }

    private function nestedQuery($field,$data)
    {
        $this->empty = false;
        $nestedField = explode('.', $field)[0]; //ex: famille.nom => famille

        $baseQuery = new \Elastica\Query\MatchAll();

        $term = new \Elastica\Filter\Term(array($field => $data));

        $boolFilter = new \Elastica\Filter\BoolFilter();
        $boolFilter->addMust($term);

        $nested = new \Elastica\Filter\Nested();
        $nested->setPath($nestedField);
        $nested->setFilter($boolFilter);

        $nestedQuery = new \Elastica\Query\Filtered($baseQuery, $nested);

        $this->boolQuery->addMust($nestedQuery);
    }


    public function addNestedTextMatch($field,$text)
    {
        if((!is_null($text)) && ($text != ''))
        {
            $text = strtolower($text);
            $this->nestedQuery($field,$text);
        }
        return $this;
    }

    public function addNestedNumber($field,$number)
    {
        if((!is_null($number)) && is_numeric($number))
        {
            $this->nestedQuery($field,$number);
        }
        return $this;
    }

    public function addNestedBoolean($field,$boolean)
    {
        if((!is_null($boolean)) && is_bool($boolean))
        {
            $this->nestedQuery($field,$boolean);
        }
        return $this;
    }

    private function numberRangeQuery($field,$number,$operation)
    {
        if((!is_null($number)) && (is_numeric($number)))
        {
            $this->empty = false;
            $query = new \Elastica\Query\Range($field,array($operation=>$number));
            $this->boolQuery->addMust($query);
        }
    }

    private function dateRangeQuery($field,$date,$operation)
    {
        if((!is_null($date)) && ($date instanceof \DateTime))
        {

            /*
             * attention petite subtilité:
             *
             * Le format par defaut du type "date" dans elastic
             * correspond au format ci-dessous.
             *
             * Donc la date qu'on compare doit être reformatée
             * au même format pour que ca marche.
             */
            $this->empty = false;
            $query = new \Elastica\Query\Range($field,
                array(
                    $operation=>$date->format('Y-m-d\TH:i:sP')
                )
            );
            $this->boolQuery->addMust($query);

        }
    }

    public function addNumberGreaterOrEqual($field,$number)
    {
        $this->numberRangeQuery($field,$number,'gte');
        return $this;
    }

    public function addNumberGreater($field,$number)
    {
        $this->numberRangeQuery($field,$number,'gt');
        return $this;
    }

    public function addNumberLessOrEqual($field,$number)
    {
        $this->numberRangeQuery($field,$number,'lte');
        return $this;
    }

    public function addNumberLess($field,$number)
    {
        $this->numberRangeQuery($field,$number,'lt');
        return $this;
    }

    public function addNumberInRange($field,$min,$max)
    {
        $this->addNumberGreaterOrEqual($field,$min);
        $this->addNumberLessOrEqual($field,$max);
        return $this;
    }

    public function addDateGreaterOrEqual($field, $date)
    {
        $this->dateRangeQuery($field,$date,'gte');
        return $this;
    }

    public function addDateLessOrEqual($field, $date)
    {
        $this->dateRangeQuery($field,$date,'lte');
        return $this;
    }

    /**
     * @param $field string
     * @param $dateMin \Datetime|null
     * @param $dateMax \Datetime|null
     * @return $this
     */
    public function addDateInRange($field,$dateMin,$dateMax)
    {
        $this->addDateGreaterOrEqual($field,$dateMin);
        $this->addDateLessOrEqual($field,$dateMax);
        return $this;
    }

    public function addBoolean($field,$boolean)
    {
        if((!is_null($boolean)) && (is_bool($boolean)))
        {
            $this->empty = false;

            $query = new \Elastica\Query\Term();
            $query->setTerm($field,$boolean);

            $this->boolQuery->addMust($query);
        }
        return $this;
    }

    public function addNumber($field,$number)
    {
        if((!is_null($number)) && (is_numeric($number)))
        {
            $this->empty = false;

            $query = new \Elastica\Query\Term();
            $query->setTerm($field,$number);

            $this->boolQuery->addMust($query);
        }
        return $this;
    }

}