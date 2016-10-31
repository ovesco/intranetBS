<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 03.10.16
 * Time: 21:59
 */

namespace AppBundle\Security;

use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\User;


class RoleHierarchy {


    /** @var RoleHierarchyBuilder  */
    private $builder;

    /** @var  array */
    private $roleHierarchy;

    /** @var  array    ex: roleList['ROLE_USER] donne le Role correspondant */
    private $roleList;

    public function __construct(RoleHierarchyBuilder $builder)
    {
        /*
         * builder create the role hierarchy and give it ot this class
         */
        $builder->build();
        $this->roleHierarchy = $builder->getHierarchy();
        $this->roleList = $builder->getAllRoles();
        $this->builder = $builder;
    }


    /**
     * Get all deducted roles for user. return like array('ROLE_ADMIN','ROLE_USER',...)
     *
     * @param User $user
     * @return array
     */
    public function getAllRolesForUser(User $user)
    {
        $roles = array_merge($user->getSelectedRoles(),$user->getMembreRoles());
        if(empty($roles))
            return array();
        return $this->getDeducedRoles(
            array_merge($user->getSelectedRoles(),$user->getMembreRoles())
        );
    }

    /**
     * Deduce roles form an array of roles based on the hierarchy
     *
     * @param array $rolesKey
     * @return array
     */
    public function getDeducedRoles($rolesKey)
    {
        if(empty($rolesKey))
            return array();//small perf and avoid logic error

        $collection = new ArrayCollection();

        foreach($rolesKey as $key)
        {
            $role = $this->getRoleByKey($key);

            if($role instanceof Role)
            {
                $childsRoles = $role->getChildsRecursive(true);

                if(!empty($childsRoles))
                {
                    /** @var Role $childRole */
                    foreach($childsRoles as $childRole)
                    {
                        if(!$collection->contains($childRole->getKey()))
                        {
                            if($this->isExistingRole($childRole->getKey()))//petit check pour avoir l'esprit tranquil
                                $collection->add($childRole->getKey());
                        }
                    }
                }
            }
        }

        return $collection->toArray();
    }

    /**
     * @param string $roleKey
     * @return Role|null
     */
    public function getRoleByKey($roleKey)
    {
        if(array_key_exists($roleKey,$this->roleList))
            return $this->roleList[$roleKey];
        else
            return null;
    }

    /**
     * return array with all the role (e.g. array(ROLE_XXX, ROLE_YYY,...) )
     *
     * @return array
     */
    public function getAllExistingRoles()
    {
        return array_keys($this->roleList);
    }

    /**
     * Check if the role is present in the hierarchy
     *
     * @param string $roleKey
     * @return bool
     */
    public function isExistingRole($roleKey)
    {
        return array_key_exists($roleKey,$this->roleList);
    }

    /**
     * @return array
     */
    public function getHierarchy()
    {
        return $this->roleHierarchy;
    }
}
