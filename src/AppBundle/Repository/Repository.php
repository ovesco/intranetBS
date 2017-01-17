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

    /**
     * Cette fonction est surtout utile pour les tests
     * lorsqu'on veut faire des tests sur des entités
     * aléatoire.
     *
     * @param $numberOfEntity
     * @return array
     * @throws \Error
     * @throws \Exception
     * @throws \TypeError
     */
    public function findRandom($numberOfEntity)
    {
        $results = $this->createQueryBuilder('e')
            ->select('e.id')
            ->getQuery()
            ->getResult();

        $ids = array();
        for($i = 0; $i < $numberOfEntity; $i++)
        {
            $randomKey = random_int(0,count($results)-1);
            $ids[] = $results[$randomKey]['id'];
        }

        return $this->findBy(array('id'=>$ids));
    }



}
