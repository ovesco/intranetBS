<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 27.10.15
 * Time: 14:59
 *
 * This class provide the annotation @Menu in controllers.
 * All the uses of @Menu sould be accopmagned by a @Route annotation.
 *
 * Exemple:
 *
 *      /**
 *       * @Route("/exemple", name="exemple")
 *       * @Menu("An Exemple", block="exemple", order=1, expanded=true, icon="add")
 *       */



namespace AppBundle\Utils\Menu;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class Menu {

    /**
     * The label used in the display
     * @var string
     */
    private $label;

    /**
     * The block were the menu is displayed
     * @var string
     */
    private $block;

    /**
     * The order of display in the block
     * @var integer
     */
    private $order;

    /**
     * True if the menu should be always shown
     * @var boolean
     */
    private $expanded;

    /**
     * The icon associated to this menu item
     * @var string
     */
    private $icon;



    /**
     * @param $options
     * @throws \InvalidArgumentException
     */
    public function __construct($options)
    {
        // default value
        $this->label = null;
        $this->block = null;
        $this->order = null;
        $this->expanded = false;
        $this->icon = null;

        //first argument @menu("label")
        if (isset($options['value'])) {
            $options['label'] = $options['value'];
            unset($options['value']);
        }

        foreach ($options as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new \InvalidArgumentException(sprintf('Property "%s" does not exist in class "%s"', $key,get_class($this)));
            }
            $this->$key = $value;
        }

    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getBlock()
    {
        return $this->block;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getExpanded()
    {
        return $this->expanded;
    }

    public function getIcon()
    {
        return $this->icon;
    }
}