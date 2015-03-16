<?php

namespace AppBundle\Utils\Data;

use AppBundle\Entity\Modification;
use AppBundle\Entity\ModificationsContainer;
use AppBundle\Utils\Accessor\Accessor;
use AppBundle\Utils\Accessor\Parser;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Doctrine\ORM\EntityManager;

use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;

/**
 * Class Validation
 * La validation permet de faire valider des entités avant de les persister en base de donnée. On peut ainsi maintenir
 * un certain contrôle sur les modifications réalisées
 * @package AppBundle\Utils\Data
 */
class Validation {


    private $em;

    private $parser;

    public $accessor;

    /**
     * Constructeur
     * @param EntityManager $em l'entityManager
     * @param Accessor $accessor l'accessor
     * @param Parser $parser le parser
     */
    public function __construct(EntityManager $em, Accessor $accessor, Parser $parser) {

        $this->em       = $em;
        $this->parser   = $parser;
        $this->accessor = $accessor;
    }

    /**
     * validateField va tenter de modifier une valeur dans la base de donnée en fonction des droits de l'utilisateur.
     * @param $newValue mixed la nouvelle valeur
     * @param $path string le chemin jusqu'à la valeur
     * @return array contenant les path requis pour pouvoir valider
     */
    public function validateField($newValue, $path) {

        $entity     = $this->accessor->extractEntity($path);
        $oldValue   = $this->accessor->getStrictProperty($entity, $this->accessor->extractSchema($path));

        $container  = $this->generateContainer($entity);
        $container  = $this->generateModification($container, $path, $oldValue, $newValue);

        $this->em->persist($container);
        $entity->setValidity(1);
        $this->em->flush();
        $this->em->getConnection()->setNestTransactionsWithSavepoints(true);

        return $this->tryToValidate($path, $container);
    }


    /**
     * La méthode tryToValidate va essayer de valider un path. Si elle y arrive, elle le fera en retournant true, si elle ne peut pas, elle
     * retournera false
     * @param $path string le path de modification
     * @param $container ModificationsContainer le container (on le passe car il n'existe pas forcement en BDD)
     * @return boolean
     */
    public function tryToValidate($path, $container) {

        $entity     = $this->accessor->extractEntity($path);
        $schema     = $this->accessor->extractSchema($path);
        $modif      = null;

        foreach($container->getModifications() as $modification)
            if($modification->getPath() == $path)
                $modif = $modification;


        $oldVal = $this->accessor->pa->getValue($entity, $schema);

        //Si il n'y a pas d'erreur ici, c'est qu'il est possible de modifier l'entité
        $this->accessor->pa->setValue($entity, $schema, $this->parser->decode($modif->getNewValue()));
        $this->em->persist($entity);
        $this->em->flush();

    }

    /**
     * Permet d'annuler toutes les modifications présentes dans le container
     * @param ModificationsContainer $container
     */
    public function cancelContainer(ModificationsContainer $container) {

        $entity = null;

        foreach($container->getModifications() as $modif) {
            if($entity == null) $this->accessor->extractEntity($modif->getPath());

            $schema = $this->accessor->extractSchema($modif->getPath());
            $this->accessor->setProperty($entity, $schema, $this->parser->decode($modif->getOldValue()));
        }

        $this->em->persist($entity);
        $this->em->remove($container);
        $this->em->flush();
    }

    /**
     * Permet d'annuler une modification. Au lieu de tout remettre comme avant, on va remplir le champ par un vide,
     * adoucissant ainsi la taille de la requête, et permet de garder l'objet lié.
     * @param Modification $modif
     */
    public function cancelModification(Modification $modif) {

        $path   = $modif->getPath();
        $entity = $this->accessor->extractEntity($path);
        $schema = $this->accessor->extractSchema($path);

        $this->accessor->pa->setValue($entity, $schema, $this->parser->decode($modif->getOldValue()));
        $this->em->persist($entity);

        /*
         * On vérifie la longueur du schéma. En effet, si celui-ci fait plus que 2, ca veut dire que l'on cible une entité
         * liée. On doit donc vérifier que la valeur a le droit d'être null, sinon c'est toute l'entité que l'on supprimmera,
         * et les path avec
         */
        if(count($modif->getModificationsContainer()->getModifications()) == 1) $this->em->remove($modif->getModificationsContainer());
        else $this->em->remove($modif);

        $this->em->flush();
    }

    /**
     * Cette méthode va hydrater un élément d'une entité à partir d'une modification
     * @param $cursor object le curseur dans le path
     * @param $container ModificationsContainer le conteneur de modifications
     * @param $path string le path
     * @param $setter string le setter
     * @return object le cursor
     */
    private function tryToHydrate($cursor, ModificationsContainer $container, $path, $setter) {

        $modif = null;

        foreach($container->getModifications() as $modif) {

            if($modif->getPath() == $path) {

                $cursor->$setter($this->parser->decode($modif->getNewValue()));
            }

        }

        return $cursor;
    }

    /**
     * Retourne la modification liée au path
     * @param $path string le path de modification
     * @return mixed la nouvelle valeur
     */
    public function getModification($path) {

        $entity = $this->accessor->extractEntity($path);
        $container = $this->em->getRepository('AppBundle:ModificationsContainer')->findOneByKey($this->getKey($entity));

        foreach($container->getModifications() as $modif) {

            if($modif->getPath() == $path)
                return $modif;
        }
    }

    /**
     * Vérifie si un conteneur de modification existe déjà pour une entité donnée, sinon en génère un
     * @param $entity object l'entity à checker
     * @return ModificationsContainer
     */
    public function generateContainer($entity) {

        $container  = $this->em->getRepository('AppBundle:ModificationsContainer')->findOneByKey($this->getKey($entity));

        if($container == null) {

            $id         = $entity->getId();
            $class     = explode('\\', \Doctrine\Common\Util\ClassUtils::getRealClass(get_class($entity)));
            $key        = $this->getKey($entity);
            $container  = new ModificationsContainer();

            $container->setKey($key);
            $container->setClass($class[count($class) -1]);
            $container->setEntityId($id);
            return $container;
        }

        else
            return $container;
    }

    /**
     * Récupère un objet modification si il en existe un dans un container, ou en génère un
     * @param ModificationsContainer $container le container
     * @param string $path le schema de modification
     * @param mixed $newValue la nouvelle valeur
     * @param mixed $oldValue l'ancienne valeur
     * @return ModificationsContainer
     */
    public function generateModification(ModificationsContainer $container, $path, $oldValue, $newValue) {

        $oldValue = $this->parser->encode($oldValue);
        $newValue = $this->parser->encode($newValue);

        foreach($container->getModifications() as $modification) {

            if($modification->getPath() == $path) {

                $modification->setNewValue($newValue);
                $this->em->persist($modification);
                return $container;
            }
        }

        $modif = new Modification();
        $modif->setDate(new \Datetime());
        $modif->setModificationsContainer($container);
        $modif->setPath($path);
        $modif->setOldValue($oldValue);
        $modif->setNewValue($newValue);

        $container->addModification($modif);
        return $container;
    }


    /**
     * Vérifie si le champs d'une entité est actuellement en cours de modification en se basant sur son path.
     * On vérifie en premier lieu si l'attribut isValid de l'entité existe, et soit true, sinon on vérifie si il existe
     * une modification liée au champ
     * @param $path string le path
     * @return boolean
     */
    public function isInModification($path) {

        $entity  = $this->accessor->extractEntity($path);

        if($entity->getValidity() == 0) return true; //Si l'entité est non valide, rien n'est modifiable

        $container = $this->em->getRepository('AppBundle:ModificationsContainer')->findOneByKey($this->getKey($entity));

        if($container == null) return false;

        foreach($container->getModifications() as $modif) {

            if($modif->getPath() == $path)
                return true;
        }

        return false;

    }

    /**
     * Génère la key d'un container
     * @param $entity object l'entité
     * @return string la key
     */
    private function getKey($entity) {

        $id         = $entity->getId();
        $class      = \Doctrine\Common\Util\ClassUtils::getRealClass(get_class($entity));
        return      md5($class . '.' . $id);
    }
}