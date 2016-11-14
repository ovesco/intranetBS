<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 20.10.16
 * Time: 20:33
 */

namespace AppBundle\Repository;


class GroupeRepository extends Repository{


    /**
     * Retourne la hierarchie complète au format JSON pour affichage dans la hierarchie
     * canvas GO.js
     */
    public function findJSONHierarchie() {

        $hierarchie             = array();
        $groupes                = $this->findAll();

        foreach($groupes as $groupe) {

            $hierarchie[] = array(

                'nom'    => $groupe->getNom(),
                'key'    => $groupe->getId(),
                'parent' => ($groupe->getParent() != null) ? $groupe->getParent()->getId() : 0
            );
        }

        return $hierarchie;
    }

    public function findHighestGroupes() {

        $queryBuilder = $this->createQueryBuilder('groupe');

        $queryBuilder->andWhere($queryBuilder->expr()->isNull('groupe.parent'));


        return $queryBuilder->getQuery()->getResult();
    }


    /**
     * @param $groupeId
     * @param null $date
     * @return float|int
     */
    public function findNumberOfMembreAtDate($groupeId,$date = null)
    {

        $queryBuilder = $this->createQueryBuilder('groupe');

        $queryBuilder->leftJoin('AppBundle\Entity\Attribution', 'attribution', 'WITH', 'groupe.id = attribution.groupe');

        if($date == null)
        {
            $date = new \DateTime();
        }

        $queryBuilder
            ->andWhere('groupe.id = :id')
            ->setParameter('id', $groupeId)
            ->andWhere('attribution.dateFin is NULL OR attribution.dateFin >= :dateFin')
            ->setParameter('dateFin', $date)
            ->andWhere('attribution.dateDebut <= :dateDebut')
            ->setParameter('dateDebut', $date);

        $queryBuilder->addSelect('COUNT(attribution) as total');

        $result = $queryBuilder->getQuery()->getScalarResult();

        if($result[0]['total'] == null)
            return 0;
        return floatval($result[0]['total']);

    }

    /**
     * Retourne un tableau contentant tout les Ids des groupes enfants
     *
     * @param $groupeId
     * @return array
     */
    public function getArrayOfChildIdsRecursive($groupeId)
    {
        $idArray = array($groupeId);

        do{

            $arrayStart = $idArray;
            $queryBuilder = $this->createQueryBuilder('groupe');
            $queryBuilder
                ->andWhere('groupe.parent IN (:ids) OR groupe.id IN (:ids)')
                ->setParameter('ids', $idArray);

            $groupes = $queryBuilder->getQuery()->getResult();

            foreach($groupes as $groupe)
            {
                $found = false;
                foreach($idArray as $id)
                {
                    if($id == $groupe->getId())
                        $found = true;

                }
                if(!$found)
                {
                    array_push($idArray,$groupe->getId());
                }

            }

        }while($arrayStart != $idArray);


        unset($idArray[0]);//on enleve id du groupe parent
        return $idArray;
    }

    /**
     * @param $groupeId
     * @param null $date
     * @return float|int
     */
    public function findNumberOfMembreAtDateRecursive($groupeId,$date = null)
    {
        $idArray = $this->getArrayOfChildIdsRecursive($groupeId);
        $membreNumber = $this->findNumberOfMembreAtDate($groupeId,$date);
        foreach($idArray as $id)
        {
            $membreNumber = $membreNumber + $this->findNumberOfMembreAtDate($id,$date);
        }
        return $membreNumber;
    }


}