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

abstract class ListModel {

    /** @var \Twig_Environment */
    protected $twig;

    /** @var Router */
    protected $router;

    /** @var AuthorizationChecker  */
    private $checker;

    public function __construct(\Twig_Environment $twig, Router $router, AuthorizationChecker $cheker)
    {
        $this->router = $router;
        $this->twig = $twig;
        $this->checker = $cheker;
    }

    /**
     * @return \Twig_Environment
     */
    public function getTwig()
    {
        return $this->twig;
    }

    /**
     * @return Router
     */
    public function getRouter()
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

}