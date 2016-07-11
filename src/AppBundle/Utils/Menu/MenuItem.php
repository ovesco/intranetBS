<?php
/**
 * User: NicolasUffer
 * Date: 27.10.15
 * Time: 18:19
 *
 * This class is the link between @Route and @Menu annotations.
 * MenuItem contain all the information needed to generate a
 * menu item and to order them in block.
 *
 *
 */

namespace AppBundle\Utils\Menu;

use AppBundle\Utils\Menu\Menu as MenuAnnotation;

/**
 * Class MenuItem
 * @package AppBundle\Utils\Menu
 *
 * All the property are public to ease the code readability.
 *
 */
class MenuItem {

    /**
     * From route
     * @var string
     */
    public $routeName;

    /**
     * From menu
     * @var string
     */
    public $label;

    /**
     * From menu
     * @var null|string
     */
    public $block;

    /**
     * From menu
     * @var int|null
     */
    public $order;

    /**
     * From menu
     * @var boolean
     */
    public $expanded;

    /**
     * From menu
     * @var null|string
     */
    public $icon;

    /**
     * @param Menu $menu
     * @param $routeName
     */
    public function __construct(MenuAnnotation $menu,$routeName)
    {
        $this->routeName = $routeName;
        //if null, label is replaced by routeName
        $this->label = ($menu->getLabel() != null ? $menu->getLabel() : $this->routeName);
        $this->block = $menu->getBlock();
        $this->order = $menu->getOrder();
        $this->expanded = $menu->getExpanded();
        $this->icon = $menu->getIcon();
    }

}