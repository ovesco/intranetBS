<?php

namespace AppBundle\Utils\ListRender;

use Twig_Environment;
use Doctrine\Common\Collections\ArrayCollection;

class ListRender {

    /** @var Twig_Environment */
    private $twig;

    /** @var Array */
    private $objects;

    private $name;

    private $searchBar;

    private $columns;

    /**
     * @param Twig_Environment $twig
     * @param Array $objects
     */
    public function __construct(Twig_Environment $twig,$objects = array()){
        $this->twig = $twig;
        $this->objects = $objects;
        $this->searchBar = false;
        $this->columns = new ArrayCollection();
    }

    /**
     * Cette fonction crée le rendu html de la liste
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

}