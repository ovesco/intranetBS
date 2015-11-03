<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 01.11.15
 * Time: 23:26
 */

namespace AppBundle\Utils\Session;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Ce service permet le stockage d'une liste d'ID en session avec des méthodes qui facilite
 * les operation d'ajout et de suppression de d'ID de la liste.
 *
 * La récupréation des objets est aussi facilité en passant en parametre le "repository" des ID stocké.
 *
 *
 * Class ListStorage
 * @package AppBundle\Utils\Session
 */
class ListStorage {

    const SESSION_STORAGE_KEY = "utils_session_list_storage_";

    /**
     * @var Session
     */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
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
     * @param $containerKey
     * @param $repository
     * @return array
     * @throws
     */
    public function getObjects($containerKey,$repository)
    {
        if(!method_exists($repository,"findBy"))
        {
            throw new \Exception("ListStorage:getObjects: can't pass repositpry without -findBy()- methode");
        }
        return $repository->findBy(array('id' => $this->getContainer($containerKey)->toArray()));
    }



}