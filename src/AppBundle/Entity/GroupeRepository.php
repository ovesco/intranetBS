<?php
namespace AppBundle\Entity;
use Doctrine\ORM\EntityRepository;


class GroupeRepository extends EntityRepository
{
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
}