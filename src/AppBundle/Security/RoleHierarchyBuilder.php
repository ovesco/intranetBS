<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 22.10.16
 * Time: 18:41
 */

namespace AppBundle\Security;

class RoleHierarchyBuilder {


    private $roleHierarchyData;

    private $roleDescriptionData;

    /** @var  Role */
    private $hierarchyRoot;

    /** @var array stored as $allRoles['ROLE_KEY'] = Role() */
    private $allRoles;

    /**
     * For input, @see AppBundle/Resources/config/roles_parameters.yml
     *
     * @param $role_hierarchy
     * @param $role_description
     */
    public function __construct($role_hierarchy, $role_description)
    {
        $this->roleHierarchyData = $role_hierarchy;
        $this->roleDescriptionData = $role_description;
        $this->hierarchyRoot = null;
        $this->allRoles = array();
    }

    public function build(){

        $this->hierarchyRoot = $this->parseRole(new Role(),$this->roleHierarchyData);
    }

    public function getHierarchy()
    {
        return $this->hierarchyRoot->getChilds();
    }

    public function getAllRoles()
    {
        return $this->allRoles;
    }

    /**
     * Cette fonction parse les informations contenues dans le parametre "role_hierarchy"
     *
     * @param Role $role
     * @param $data
     * @return Role
     */
    private function parseRole(Role $role,$data)
    {
        if(is_array($data))
        {
            foreach($data as $roleKey => $childs)
            {
                $child = new Role($roleKey);
                $this->searchDescription($child);
                $child->setParent($role);
                $role->addChild($child);
                //rajoute le role dans le tableau non hierarchique

                //parse les enfants de ce role
                $this->parseRole($child,$childs);
            }
        }
        if($role->getKey() != null)
            $this->allRoles[$role->getKey()] = $role;
        return $role;
    }

    /**
     * Set the description of role if the description is set in parameter "role_description"
     *
     * @param Role $role
     */
    private function searchDescription(Role $role)
    {
        if($role->getKey() != null)
        {
            if(array_key_exists($role->getKey(),$this->roleDescriptionData))
            {
                $role->setDescription($this->roleDescriptionData[$role->getKey()]);
            }
        }
    }





}