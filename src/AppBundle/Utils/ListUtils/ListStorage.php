<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 01.11.15
 * Time: 23:26
 */


namespace AppBundle\Utils\ListUtils;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;

/**
 * Ce service permet le stockage d'une liste d'ID en session avec des méthodes qui facilite
 * les operation d'ajout et de suppression de d'ID de la liste.
 *
 * La récupréation des objets est aussi facilité en passant en parametre le "repository" des ID stocké.
 *
 *
 * Class ListStorage
 * @package AppBundle\Utils\ListRenderer
 */
class ListStorage {

    const SESSION_STORAGE_KEY = "utils_session_list_storage_";
    const REPOSITORY_KEY = "repository";
    const MODEL_KEY = "model";

    /**
     * @var Session
     */
    private $session;

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(Session $session,EntityManager $em)
    {
        $this->session = $session;
        $this->em = $em;
    }


    /**
     * @param string $containerKey
     * @param string $model
     */
    public function setModel($containerKey,$model)
    {
        $key = ListStorage::SESSION_STORAGE_KEY.$containerKey.ListStorage::MODEL_KEY;
        $this->session->set($key,$model);
    }

    /**
     * @param string $containerKey
     * @return string
     */
    public function getModel($containerKey)
    {
        $key = ListStorage::SESSION_STORAGE_KEY.$containerKey.ListStorage::MODEL_KEY;
        return $this->session->get($key);
    }


    /**
     * @param string $containerKey
     * @param string $entityRepositoryName
     */
    public function setRepository($containerKey,$entityRepositoryName)
    {
        $key = ListStorage::SESSION_STORAGE_KEY.$containerKey.ListStorage::REPOSITORY_KEY;
        $this->session->set($key,$entityRepositoryName);
    }

    /**
     * @param string $containerKey
     * @return string
     */
    public function getRepository($containerKey)
    {
        $key = ListStorage::SESSION_STORAGE_KEY.$containerKey.ListStorage::REPOSITORY_KEY;
        return $this->session->get($key);
    }

    /**
     * @param string $containerKey
     * @return ArrayCollection
     */
    private function getContainer($containerKey)
    {
        $key = ListStorage::SESSION_STORAGE_KEY.$containerKey;
        return $this->session->get($key,new ArrayCollection());
    }

    /**
     * @param string $containerKey
     * @param ArrayCollection $container
     */
    private function setContainer($containerKey,ArrayCollection $container)
    {
        $key = ListStorage::SESSION_STORAGE_KEY.$containerKey;
        $this->session->set($key,$container);
    }


    /**
     * @param String $containerKey
     * @param mixed $object
     * @throws \Exception
     */
    public function addObject($containerKey,$object)
    {
        if(!method_exists($object,"getId"))
        {
            throw new \Exception("ListStorage: can't store objects without -getId()- methode");
        }
        /** @var ArrayCollection $container */
        $container = $this->getContainer($containerKey);
        if(!$container->contains($object->getId()))
        {
            $container->add($object->getId());
            $this->setContainer($containerKey,$container);
        }
    }

    /**
     * @param String $containerKey
     * @param mixed $object
     * @throws \Exception
     */
    public function removeObject($containerKey,$object)
    {
        if(!method_exists($object,"getId"))
        {
            throw new \Exception("ListStorage: can't store objects without -getId()- methode");
        }
        /** @var ArrayCollection $container */
        $container = $this->getContainer($containerKey);
        if($container->contains($object->getId()))
        {
            $container->removeElement($object->getId());
            $this->setContainer($containerKey,$container);
        }
    }

    /**
     * @param String $containerKey
     * @param Array|ArrayCollection $objects
     * @throws \Exception
     */
    public function addObjects($containerKey,$objects)
    {
        foreach($objects as $object)
        {
            $this->addObject($containerKey,$object);
        }
    }

    /**
     * @param String $containerKey
     * @param Array|ArrayCollection $objects
     * @throws \Exception
     */
    public function removeObjects($containerKey,$objects)
    {
        foreach($objects as $object)
        {
            $this->removeObject($containerKey,$object);
        }
    }

    /**
     * @param String $containerKey
     * @param Array|ArrayCollection $objects
     * @throws \Exception
     */
    public function setObjects($containerKey,$objects)
    {
        //clear the container
        /** @var ArrayCollection $container */
        $container = $this->getContainer($containerKey);
        $container->clear();
        $this->setContainer($containerKey,$container);

        //add the new objects
        foreach($objects as $object)
        {
            $this->addObject($containerKey,$object);
        }
    }


    /**
     * @param $containerKey
     * @return array
     * @throws
     */
    public function getObjects($containerKey)
    {
        $repoName = $this->getRepository($containerKey);

        $repository = $this->em->getRepository($repoName);

        if(!method_exists($repository,"findBy"))
        {
            throw new \Exception("ListStorage:getObjects: can't pass repositpry without -findBy()- methode");
        }
        return $repository->findBy(array('id' => $this->getContainer($containerKey)->toArray()));
    }



}