<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 03.11.15
 * Time: 16:50
 */

namespace AppBundle\Utils\Elastic;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ElasticToDoctrine
 * @package AppBundle\Utils\Elastic
 */
class ElasticToDoctrine {

    /** @var  ContainerInterface */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container){

        $this->container = $container;
    }

    /**
     * @param string $elasticService
     * @param string $doctrineRepository
     * @param string $query
     * @return mixed
     */
    public function convert($elasticService,$doctrineRepository,$query)
    {
        $matchedEntities = $this->container->get($elasticService)->find($query);

        $ids = array();
        foreach($matchedEntities as $matchedEntity)
        {
            $ids[] = $matchedEntity->getId();
        }
        return $this->container->get('doctrine.orm.entity_manager')->getRepository($doctrineRepository)->findById($ids);
    }

}