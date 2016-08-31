<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 31.08.16
 * Time: 16:05
 */

namespace AppBundle\Handler;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Repository;

class EntityHandler {

    /** @var EntityManager */
    protected $em;
    /** @var string */
    protected $entityClass;

    /** @var Repository */
    protected $repository;

    public function __construct(EntityManager $em, $entityClass)
    {
        $this->em = $em;
        $this->entityClass = $entityClass;
        $this->repository = $this->em->getRepository($this->entityClass);
    }


    /**
     * @param $id
     * @return null|object
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }


    /**
     * @param $idsArray
     * @return array
     */
    public function getCollection($idsArray)
    {
        return $this->repository->findBy(array('id'=>$idsArray));
    }

    public function getAll()
    {
        return $this->repository->findAll();
    }




}