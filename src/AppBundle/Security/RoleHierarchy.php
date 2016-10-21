<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 03.10.16
 * Time: 21:59
 */

namespace AppBundle\Security;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RoleHierarchy {


    /** @var ContainerInterface  */
    private $container;

    /** @var mixed contient les donnée du fichier de parametre "roles_parametres.yml" */
    private $roleHierarchyData;
    
    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->roleHierarchyData = $this->container->getParameter('role_hierarchy');
    }


    /**
     * Deduce roles form an array of roles based on the hiearchy
     *
     * @param array $roles
     * @return array
     */
    public function getDeducedRoles($roles)
    {
        $collection = new ArrayCollection();

        foreach($roles as $role)
        {
            $deducedRoles = $this->getSubRoles($role);

            foreach($deducedRoles as $deducedRole)
            {
                if(!$collection->contains($deducedRole))
                {
                    if($this->isExistingRole($deducedRole))//petit check pour avoir l'esprit tranquil
                        $collection->add($deducedRole);
                }
            }
        }

        return $collection->toArray();
    }



    /**
     * Return all the role under the $role in the hierarchy (and with/without himself optionaly)
     *
     * @param $role
     * @param bool $selfInclude
     * @return array
     */
    public function getSubRoles($role,$selfInclude = true)
    {
        $childsRole = $this->findSubArrayByKey($this->roleHierarchyData,$role);

        if($childsRole == null)
        {
            return ( $selfInclude ? array($role) : array() );
        }

        $deductedRoles = $this->extractKeys($childsRole);

        if($selfInclude)
        {
            $deductedRoles[] = $role;
        }

        return $deductedRoles;
    }

    /**
     * Check if the role is present in the hierarchy
     *
     * @param $role
     * @return bool
     */
    public function isExistingRole($role)
    {
        $allRoles = $this->extractKeys($this->roleHierarchyData);

        return in_array($role,$allRoles);

    }


    /**
     * cette methode permet d'extraire les infos de la hierarchy stockée dans "roles_parameters.yml"
     *
     * @param $array
     * @return array
     */
    private function extractKeys($array)
    {
        $keys = array();
        foreach($array as $key => $value)
        {
            $keys[] = $key;
            if(is_array($value))
            {
                $childKeys = $this->extractKeys($value);
                $keys = array_merge($keys,$childKeys);
            }
        }
        return $keys;
    }

    /**
     * cette methode permet d'extraire les infos de la hierarchy stockée dans "roles_parameters.yml"
     *
     * @param $arrayData
     * @param $searchRole
     * @return null
     */
    private function findSubArrayByKey($arrayData,$searchRole)
    {
        foreach($arrayData as $key => $array)
        {
            if($key == $searchRole)
            {
                return $array;
            }
            else
            {
                if(is_array($array))
                {
                    $subArray = $this->findSubArrayByKey($array,$searchRole);

                    if($subArray != null)
                    {
                        return $subArray;
                    }
                }
            }
        }
        return null;
    }

}