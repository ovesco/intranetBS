<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 20.10.16
 * Time: 20:33
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class Repository
 * @package AppBundle\Repository
 *
 * cette class permet de crée des méthodes utile dans chacun de nos repository
 *
 *
 */
class Repository extends EntityRepository{

    public function save($entity){
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function remove($entity)
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

}