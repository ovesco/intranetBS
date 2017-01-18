<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 20.12.16
 * Time: 21:37
 */

namespace AppBundle\Utils\ListUtils;

use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Utils\Response\ResponseFactory;

abstract class AbstractList {

    const FORMAT_INCLUDE_HTML = 'include_html';
    const FORMAT_EXPORT_CSV = 'export_csv';
    const FORMAT_EXPORT_XLSX = 'export_xlsx';

    /** @var ContainerInterface */
    protected $container;

    /** @var \Twig_Environment */
    protected $twig;

    /** @var Router */
    protected $router;

    /** @var AuthorizationChecker  */
    protected $checker;

    /** @var Array
     * Items of the list we want to show : the object */
    protected $items;

    /** @var ArrayCollection */
    protected $columns;

    /** @var String */
    protected $name;

    /** @var ArrayCollection */
    protected $actionsLine;

    /** @var ArrayCollection */
    protected $actionsList;

    /** @var bool */
    protected $datatable;

    /** @var String */
    protected $cssClass;

    /** @var  string */
    protected $url;

    /** @var ArrayCollection  */
    protected $export_formats;

    /** @var String */
    protected $itemIdAccessor;

    public function __construct(ContainerInterface $container, $itemIdAccessor = null)
    {
        $this->container = $container;
        $this->router = $container->get('router');
        $this->twig = $container->get('twig');
        $this->checker = $container->get('security.authorization_checker');

        $this->columns = new ArrayCollection();
        $this->actionsLine = new ArrayCollection();
        $this->actionsList = new ArrayCollection();
        $this->datatable = true;
        $this->name = 'list';

        $this->export_formats = new ArrayCollection();


        //most of the time objects have getId() funciton.
        if ($itemIdAccessor == null) {
            $this->itemIdAccessor = function ($obj) {
                return $obj->getId();
            };
        } else {
            $this->itemIdAccessor = $itemIdAccessor;
        }
    }

    /**
     * This is the obligatory list for each sub-class.
     *
     * @param $items
     * @param null $url
     * @return AbstractList
     */
    abstract public function getDefault($items, $url = null);


    /**
     * Cette fonction permet de faire le rendu des listes
     * selon le format, on aurra une réponse différent.
     *
     * @param string $format
     * @return mixed
     */
    public function render($format = self::FORMAT_INCLUDE_HTML)
    {
        switch($format)
        {
            case self::FORMAT_INCLUDE_HTML:
                return $this->twig->render('AppBundle:Templates:list_template.html.twig',array('list' => $this));

            case self::FORMAT_EXPORT_CSV:
                $response = $this->twig->render('AppBundle:Templates:list_template.csv.twig',array('list' => $this));
                $type = 'text/csv';
                return ResponseFactory::streamFile($response,$this->name.'.csv',$type);

            case self::FORMAT_EXPORT_XLSX:
                $export = new ListToExcel($this->container->getParameter('cache_temporary_documents_dir'));
                $file = $export->generateExcel($this);
                $type = 'application/vnd.ms-excel';
                return ResponseFactory::sendFile($file,$this->name.'.xlsx',$type);

        }
    }


    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * @return \Twig_Environment
     */
    protected function getTwig()
    {
        return $this->twig;
    }

    /**
     * @return Router
     */
    protected function getRouter()
    {
        return $this->router;
    }

    /**
     * @param $attributes
     * @param null $object
     * @return bool
     */
    public function isGranted($attributes, $object = null)
    {
        return $this->checker->isGranted($attributes, $object);
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
    protected function setItems($items)
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
    protected function setName($name)
    {
        $this->name = $name;
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
    protected function setDatatable($datatable)
    {
        $this->datatable = $datatable;
    }

    public function getColumns()
    {
        return $this->columns;
    }


    protected function addColumn(Column $col)
    {
        $this->columns->add($col);
        return $this;
    }

    protected function addActionLine(ActionLine $action)
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

    protected function addActionList(ActionList $action)
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
    protected function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return array
     */
    public function getExportFormats()
    {
        return $this->export_formats;
    }

    public function hasExportFormats()
    {
        return !$this->export_formats->isEmpty();
    }

    protected function addExportFormats($format,$label)
    {
        if(
            ($format == self::FORMAT_EXPORT_CSV)    ||
            ($format == self::FORMAT_EXPORT_XLSX)
        )
        {
            $this->export_formats->set($format,$label);
        }
    }

    /**
     * @return String
     */
    public function getCssClass()
    {
        return $this->cssClass;
    }

    /**
     * @param String $cssClass
     */
    protected function setCssClass($cssClass)
    {
        $this->cssClass = $cssClass;
    }


}