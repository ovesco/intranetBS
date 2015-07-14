<?php

namespace AppBundle\Utils\ListRender;

use Twig_Environment;
use Doctrine\Common\Collections\ArrayCollection;

class ListRender {

    /** @var Twig_Environment */
    private $twig;

    /** @var Array */
    private $objects;

    private $rawId;

    private $name;

    private $searchBar;

    private $columns;

    private $headers;

    /**
     * @param Twig_Environment $twig
     * @param array $objects
     * @param null $rawId
     */
    public function __construct(Twig_Environment $twig,$objects = array(),$rawId = null){
        $this->twig = $twig;
        $this->objects = $objects;
        $this->searchBar = false;
        $this->columns = new ArrayCollection();
        $this->headers = new ArrayCollection();

        //most of the time objects have getId funciton.
        if($rawId == null)
        {
            $this->rawId = function($obj){return $obj->getId();};
        }
        else
        {
            $this->rawId = $rawId;
        }

    }

    /**
     * Cette fonction crée le rendu html de la liste
     * en appelant le template Twig des listes.
     *
     * @return string
     */
    public function render(){

        return $this->twig->render(
            'AppBundle:Templates:listeRender.html.twig',
            array('list'=>$this)
        );
    }


    /**
     * @param Array $objects
     */
    public function setObjects($objects){
        $this->objects = $objects;
    }

    /**
     * @return Array
     */
    public function getObjects(){
        return $this->objects;
    }

    /**
     * @param string $name
     */
    public function setName($name){
        $this->name = $name;
    }

    public function getName(){
        return $this->name;
    }


    /**
     * Si true alors active la bar de recherche
     *
     * @param boolean $bool
     */
    public function setSearchBar($bool)
    {
        $this->searchBar = $bool;
    }

    public function hasSearchBar()
    {
        return $this->searchBar;
    }

    /**
     * check si l'un des "tools" est activé
     *
     * @return bool
     */
    public function hasToolbar(){
        return $this->searchBar;
    }

    public function addColumn(Column $col){

        $this->columns->add($col);
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getRawId($objectOfThisRaw)
    {
        /*
         * Oui, oui, ca parait bizarre mais ca marche!!
         */
        $function = $this->rawId;
        return $function($objectOfThisRaw);
    }



}