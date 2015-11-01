<?php
/**
 * Created by PhpStorm.
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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route as RouteAnnotation;
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
    public $path;

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

    public function __construct(MenuAnnotation $menu,RouteAnnotation $route)
    {
        $this->routeName = $route->getName();
        $this->path = $route->getPath();
        //if null, label is replaced by path
        $this->label = ($menu->getLabel() != null ? $menu->getLabel() : $this->path);
        $this->block = $menu->getBlock();
        $this->order = $menu->getOrder();
        $this->expanded = $menu->getExpanded();
        $this->icon = $menu->getIcon();
    }

}