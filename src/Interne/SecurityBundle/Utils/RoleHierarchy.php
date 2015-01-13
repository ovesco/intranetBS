<?php

namespace Interne\SecurityBundle\Utils;

class RoleHierarchy extends \Symfony\Component\Security\Core\Role\RoleHierarchy
{
    /**
     * @var \Doctrine\ORM\EntityManager $entityManager
     */
    private $em;

    /**
     * On redéfinit le constructeur pour fournir au RoleHierarchy un array de roles qu'on a nous-même créé
     * à partir de la base de donnée
     * @param array $hierarchy
     */
    public function __construct(array $hierarchy, \Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;

        parent::__construct($this->buildRolesTree());
    }

    /**
     * On construit ici un array de roles hierarchy similaire à celui qu'on trouve dans security.yml
     * @return array
     */
    private function buildRolesTree()
    {
        $hierarchy = array();
        $roles     = $this->em->createQuery('select r from InterneSecurityBundle:Role r')->execute();

        foreach ($roles as $role) {

            /**
             * @var \Interne\SecurityBundle\Entity\Role $role
             */
            if ($role->getParent()) {

                if (!isset($hierarchy[$role->getParent()->getName()]))
                    $hierarchy[$role->getParent()->getName()] = array();

                $hierarchy[$role->getParent()->getName()][] = $role->getName();

            }

            else {

                if (!isset($hierarchy[$role->getName()]))
                    $hierarchy[$role->getName()] = array();

            }
        }

        return $hierarchy;
    }
}