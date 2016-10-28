<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 21.10.16
 * Time: 22:13
 */

namespace AppBundle\Security;


class Role {

    /** @var string|null */
    private $key; //e.g. ROLE_USER

    /** @var string|null */
    private $description;

    /** @var Role|null */
    private $parent;

    /** @var array */
    private $childs;


    public function __construct($key = null)
    {
        $this->key = $key;
        $this->parent = null;
        $this->childs = array();
        $this->description = null;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return array
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * @param array $childs
     */
    public function setChilds($childs)
    {
        $this->childs = $childs;
    }

    /**
     * @param Role $child
     */
    public function addChild(Role $child)
    {
        $this->childs[] = $child;
    }


    /**
     * @return null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param null $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getChildsRecursive($selfInclude = true)
    {
        $childsRecursive = array();

        $childs = $this->childs;

        if(!empty($childs))
        {
            /** @var Role $child */
            foreach($childs as $child)
            {
                $childsRecursive = array_merge($childsRecursive,$child->getChildsRecursive(true));
            }
        }

        if($selfInclude)
            $childsRecursive[] = $this;

        return $childsRecursive;
    }

}