<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 31.08.16
 * Time: 15:20
 */

namespace AppBundle\Utils\Serializer;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;

/**
 * Class EntitySerializer
 * @package AppBundle\Utils\Serializer
 *
 * In this class we use JSM serializer but we can change later
 * if we want. The goal of this class is to provide methode to
 * serilaize/deserialize objects everywhere without the
 * probleme of choosing the serializer (jsm/or other)
 */
abstract class EntitySerializer {

    /** @var Serializer */
    private $serializer;

    public function __construct(Serializer $serializer){
        $this->serializer = $serializer;
    }

    /**
     * should return somthing like ''AppBundle\Entity\Membre''
     * @return mixed
     */
    abstract protected function getEntityClass();

    public function serialize($entity){
        return $this->serializer->serialize($entity, 'json',SerializationContext::create()->enableMaxDepthChecks());
    }


    public function deserialize($json)
    {
        return $this->serializer->deserialize($json,$this->getEntityClass(), 'json');
    }
}