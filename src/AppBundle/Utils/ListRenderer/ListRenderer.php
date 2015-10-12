<?php

namespace AppBundle\Utils\ListRenderer;

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
    private $actions;

    /** @var bool */
    private $datatable;

    /** @var String */
    private $style;



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
        $this->actions = new ArrayCollection();
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
            'AppBundle:Templates:liste_renderer.html.twig',
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
    }

    /**
     * @return ArrayCollection
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param Action $action
     */
    public function addAction($action)
    {
        $this->actions->add($action);
    }


    public function getRowId($item)
    {
        /* Oui, oui, ca parait bizarre mais ca marche!! */
        $function = $this->itemIdAccessor;
        return $function($item);
    }
}
