<?php

namespace AppBundle\Utils\Accessor;
use Doctrine\ORM\EntityManager;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class Accessor
 * L'accessor permet d'accéder à des données d'entités suivant un schéma d'accès prédéfini du type
 * param1__param2__param3... ce qui rendra quelque chose comme $entity->getParam1()->getParam2()->...
 * @package AppBundle\Utils\Accessor
 */
class Accessor {

    public $pa;
    private $em;

    public function __construct(PropertyAccessor $propertyAccess, EntityManager $em) {

        $this->pa = $propertyAccess;
        $this->em = $em;
    }

    /**
     * renvoie l'entité mère liée au schéma
     * @param $schema string le schema
     * @return object l'entité
     */
    public function extractEntity($schema) {

        $data = explode('.', $schema);
        return $this->em->getRepository('AppBundle:' . ucfirst($data[0]))->find($data[1]);
    }

    /**
     * Extrait le schéma principal d'un path, c'est à dire en enlevant les deux premiers paramètres
     * qui sont l'entité et son id
     * @param $schema string le schema
     * @return string le schema principal
     */
    public function extractSchema($schema) {

        $data   = explode('.', $schema);
        $return = '';

        for($i = 2; $i < count($data); $i++) {

            $return .= $data[$i];
            if($i < count($data)-1)
                $return .= '.';
        }

        return $return;
    }

    /**
     * Retourne une proprieté
     * @param $entity object l'entité
     * @param $schema string le schéma d'accès à l'entité
     * @param string $separator string le séparateur du schema
     * @return mixed
     */
    public function getProperty($entity, $schema, $separator = '.') {

        $schema     = str_replace($separator, '.', $schema);
        $properties = explode('.', $schema);
        $this->fillEntity($entity, $schema);

        return $this->pa->getValue($entity, $schema);
    }

    /**
     * Retourne une proprieté sans utiliser fillEntity
     * @param $entity object l'entité
     * @param $schema string le schéma d'accès à l'entité
     * @return mixed
     */
    public function getStrictProperty($entity, $schema) {

        try{
            return $this->pa->getValue($entity, $schema);
        } catch(UnexpectedTypeException $e) {

            return null;
        }
    }

    /**
     * Set une valeur à une proprieté sur une entité, en suivant le schéma fourni.
     * @param $entity object l'entité
     * @param $schema string le schéma du path
     * @param $value mixed la valeur à attribuer
     * @param string $separator string le séparateur du schema
     */
    public function setProperty($entity, $schema, $value, $separator = '.') {

        $em = $this->em;

        $this->fillEntity($entity, $schema, $separator); //On vérifie l'entité
        $this->pa->setValue($entity, $schema,$value);
    }

    /**
     * Cette méthode va remplir l'entité en suivant le schéma fourni, là où c'est nécessaire. Par exemple, si l'on souhaite
     * vérifier ou récupérer la rue d'une adresse à null, ca soulève une erreur. Ici, on fournit une adresse à l'entité
     * @param $entity object l'entité
     * @param $schema string le chemin (famille.pere.adresse.rue)
     * @param $separator string le separateur
     * @return object
     */
    public function fillEntity($entity, $schema, $separator = '.')
    {

        $cursor = $entity;
        $data = explode($separator, $schema);

        for ($i = 0; $i < count($data) - 1; $i++) {

            $getter = 'get' . ucfirst($data[$i]);
            $setter = 'set' . ucfirst($data[$i]);


            if ($cursor->$getter() == null)
                $cursor->$setter($this->targetDummie($data[$i], $cursor));


            $cursor = $cursor->$getter();
        }

        return $entity;
    }

    /**
     * Renvoie l'entité liée par relation, en scannant les annotations de la classe
     * Cette méthode c'est le saint-graal, c'est abusé
     * @param $item path_item le nom de l'attribut
     * @param $cursor object l'objet qui contient l'attribut
     */
    public function targetDummie($item, $cursor) {

        $emptyObjectName    = \Doctrine\Common\Util\ClassUtils::getRealClass(get_class($cursor));
        $metaData           = $this->em->getClassMetadata($emptyObjectName);
        $target             = $metaData->associationMappings[$item]['targetEntity'];

        return new $target();
    }
}