<?php

namespace AppBundle\Utils\ListUtils;

use Doctrine\Common\Collections\ArrayCollection;
use Twig_Environment;


class ListRenderer
{
    /** @var Array
     * Items of the list we want to show : the object */
    private $items;

    /** @var ArrayCollection */
    private $columns;

    /** @var Twig_Environment */
    private $twig;

    /** @var String */
    private $itemIdAccessor;

    /** @var String */
    private $name;

    /** @var bool */
    private $searchBar;

    /** @var bool */
    private $toolbar;

    /** @var ArrayCollection */
    private $actionsLine;

    /** @var ArrayCollection */
    private $actionsList;

    /** @var bool */
    private $datatable;

    /** @var String */
    private $style;

    /** @var  string */
    private $url;


    /**
     * @param Twig_Environment $twig
     * @param array $items
     * @param null $itemIdAccessor
     */
    public function __construct(Twig_Environment $twig, $items = array(), $itemIdAccessor = null)
    {
        $this->twig = $twig;
        $this->items = $items;
        $this->searchBar = false;
        $this->toolbar = false;
        $this->columns = new ArrayCollection();
        $this->actionsLine = new ArrayCollection();
        $this->actionsList = new ArrayCollection();
        $this->datatable = true;
        $this->style = '';


        //most of the time objects have getId funciton.
        if ($itemIdAccessor == null) {
            $this->itemIdAccessor = function ($obj) {
                return $obj->getId();
            };
        } else {
            $this->itemIdAccessor = $itemIdAccessor;
        }

    }


    /**
     * Cette fonction crée le rendu html de la liste
     * en appelant le template Twig des listes.
     *
     * @return string
     */
    public function render()
    {
        return $this->twig->render(
            'AppBundle:Templates:list_template.html.twig',
            array('list' => $this)
        );
    }

    /**
     * @return Array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param Array $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
    public function hasToolbar()
    {
        return $this->toolbar;
    }


    /**
     * @return boolean
     */
    public function isDatatable()
    {
        return $this->datatable;
    }

    /**
     * @param boolean $datatable
     */
    public function setDatatable($datatable)
    {
        $this->datatable = $datatable;
    }

    /**
     * @return String
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @param String $style
     */
    public function setStyle($style)
    {
        $this->style = $style;
    }


    public function getColumns()
    {
        return $this->columns;
    }


    public function addColumn(Column $col)
    {
        $this->columns->add($col);
        return $this;
    }

    public function addActionLine(ActionLine $action)
    {
        $this->actionsLine->add($action);
    }

    /**
     * @return ArrayCollection
     */
    public function getActionsLine()
    {
        return $this->actionsLine;
    }

    public function addActionList(ActionList $action)
    {
        $this->actionsList->add($action);
    }

    /**
     * @return ArrayCollection
     */
    public function getActionsList()
    {
        return $this->actionsList;
    }

    public function getRowId($item)
    {
        /* Oui, oui, ca parait bizarre mais ca marche!! */
        $function = $this->itemIdAccessor;
        return $function($item);
    }

    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return string
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }
}
