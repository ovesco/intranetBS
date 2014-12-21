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

        /*
         * On va en premier lieu tenter de récupérer la valeur actuelle du champ. Si cela est possible, c'est-à-dire
         * qu'il est logiquement possible de modifier cette valeur de manière instantannée. On va donc utiliser le
         * proprertyAccessor pour modifier l'entité
         */
        try {

            $oldVal = $this->accessor->pa->getValue($entity, $schema);

            //Si il n'y a pas d'erreur ici, c'est qu'il est possible de modifier l'entité
            $this->accessor->pa->setValue($entity, $schema, $this->parser->decode($modif->getNewValue()));
            $this->em->persist($entity);
            //$this->em->flush();

        } catch(UnexpectedTypeException $e) {

            /*
             * on a pa pu accéder à l'ancienne valeur, on va donc devoir parcourir tout le chemin du path, instancier
             * les entités nécessaires au fur et à mesure et les hydrater avec les données présentes dans le container de
             * modifications. Si on y arrive, c'est cool, sinon on fait rien et on attend une autre modification pour retenter
             * le coup
             */

            $params     = explode('.', $schema);                                            //Les paramètres contenus dans le schema
            $cursor     = $entity;                                                          //On initialise un curseur sur l'entité
            $cursorPath = explode('.', $path)[0] . '.' . explode('.', $path)[1] . '.';      //On initialise le path de base en fonction du curseur
            $Ccursor    = 2;                                                                //La profondeur du curseur dans la hierarchie du path
            $required   = array();                                                          //Contiendra tous les paths eventuels nécessaires en cas d'echec de validation
            $out        = false;                                                            //Si on doit interrompre le persistage

            for($i = 0; $i < count($params)-1; $i++) {                                      //-1 sur les paramètres pour ne cibler que les entités du path (dernier paramètres = valeur)

                $getter = 'get' . ucfirst($params[$i]);                                     //Le getter pour le paramètre
                $setter = 'set' . ucfirst($params[$i]);                                     //Le setter pour le paramètre


                if($cursor->$getter() == null) {                                            //Si le getter si le curseur est null -> entité vide

                    $cursor->$setter($this->accessor->targetDummie($params[$i], $cursor));  //Hydratation du paramètre
                    $tempCursor = $cursor->$getter();                                       //On cible l'entité nouvellement créée
                    $cursorPath .= explode('.', $path)[$Ccursor] . '.';                     //On complète le path du curseur
                    $Ccursor++;                                                             //On incrémente la profondeur

                    $reflection = new \ReflectionClass(get_class($tempCursor));
                    $methods    = $reflection->getMethods();                                //On récupère toutes les méthodes de la nouvelle entité
                    $metadata   = $this->em->getClassMetadata(get_class($tempCursor));      //On récupère les metadatas

                    /*
                     * Pour chaque méthode de l'objet, on va chercher les setters, regarder si on a une valeur potentielle
                     * à leur assigner en générant le path jusqu'ici et assigner la valeur. Ensuite on essaie de persister
                     */
                    foreach($methods as $method) {

                        $methodType     = substr($method->name, 0, 3);
                        $methodCorpus   = lcfirst(substr($method->name, 3));

                        if($methodType == 'set') {

                            $tempPath    = $cursorPath . $methodCorpus;                                                 //On génère le path temporaire
                            $tempSetter = $method->name;                                                                //On génère le setter temporaire

                            /*
                             * On regarde si la proprieté a le droit d'être null, sinon, on la stocke dans le
                             * $required pour les renvoyer de manière à voir quels champs il manque pour valider
                             */
                            if(isset($metadata->fieldMappings[$methodCorpus]))
                                if(!$metadata->fieldMappings[$methodCorpus]['nullable'])                                //Non nullable
                                    $required[] = $tempPath;

                            /*
                             * Il se peut que les proprietés ne puissent être modifiées par l'utilisateur. Par exemple,
                             * le sexe des parents. Il est implicite, mais requis. De ce fait, on vérifie dans la liste
                             * suivante si ca correspond, et on l'hydrate directement.
                             */
                            if($methodCorpus == 'sexe') {
                                $parent = explode('.', $tempPath)[2];   //On cible le parent (mere, pere)
                                if($parent == 'mere')
                                    $tempCursor->$tempSetter('f');
                                else
                                    $tempCursor->$tempSetter('m');
                            }

                            else
                                $tempCursor = $this->tryToHydrate($tempCursor, $container, $tempPath, $tempSetter);   //On hydrate le curseur
                        }
                    }

                    $cursor->$setter($tempCursor);


                    /*
                     * NE FONCTIONNE PAS
                     * On va scanner si il pourrait y avoir des proprietés plus loin par rapport au derniner élément du path
                     * Par exemple, si on a persisté un père, on va vérifier si il y a pas des modifs suffisantes qui permettent
                     * de valider son adresse par exemple
                     *
                    $fulledPath = trim($cursorPath, '.');

                    foreach($container->getModifications() as $modif) {
                        if(strpos($modif->getPath(), $fulledPath) !== false) {
                            if( count(explode('.', $modif->getPath())) > count(explode('.', $fulledPath)) + 1 ) //On a atteint un niveau de modifications à vérifier
                                if($this->accessor->getStrictProperty($entity, $this->accessor->extractSchema($modif->getPath())) == null)
                                    $this->tryToValidate($modif->getPath(), $container);
                        }
                    }
                    */
                }

                /*
                 * Le curseur n'est pas null, ca veut dire que l'entité existe déjà à cet endroit du path, on va donc simplement
                 * le récupérer sans oublier de complémenter le path curseur et l'indice de prodondeur
                 */
                else{

                    $cursorPath .= explode('.', $path)[$Ccursor] . '.';                     //On complète le path du curseur
                    $Ccursor++;                                                             //On incrémente la profondeur
                }

                $cursor = $cursor->$getter();
            }

            /*
             * A ce niveau là, notre entité a été hydratée totalement suivant le path de base, avec toutes les modifications
             * qui étaient disponibles pour tenter d'hydrater l'objet suivant ce path. On va maintenant tenter de persister
             * l'objet en base de donnée
             */
            $this->em->persist($entity);

            try {

                $this->em->flush();

            } catch(NotNullConstraintViolationException $e) {

                /*
                 * L'entité n'a pas pu être persistée, on va donc retourner l'array des required, qui contient les path
                 * de tous les champs à remplir pour pouvoir valider ce path. On le retourne à la page, et on colorie les
                 * TD concernés
                 */
                return $required;
            }
        }
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