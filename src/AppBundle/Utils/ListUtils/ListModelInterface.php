<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 10.11.15
 * Time: 11:53
 */

namespace AppBundle\Utils\ListUtils;

use Symfony\Component\Routing\Router;

interface ListModelInterface {

    static public function getRepresentedClass();

    static public function getDefault(\Twig_Environment $twig, Router $router, $items, $url = null);

}