<?php

namespace AppBundle\Annotations\Hydrator;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Inflector\Inflector;

class ExcelHydrator
{
    public static function hydrateObject($entityClass, $data)
    {
        $reader         = new AnnotationReader();
        $reflectionObj  = new \ReflectionObject(new $entityClass);

        foreach($data as $key => $value) {

            $property = Inflector::camelize($key);

            if($reflectionObj->hasProperty($property)) {

                $reflectionProp = new \ReflectionProperty($entityClass, $property);
                $relation       = $reader->getPropertyAnnotation($reflectionProp, 'AppBundle\\Annotations\\Annotation\\Excel');

                if($relation) {

                }
            }
        }
    }
}