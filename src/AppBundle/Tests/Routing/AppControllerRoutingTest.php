<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 23.11.15
 * Time: 14:31
 */

namespace AppBundle\Tests\Routing;

/**
 * @group routing
 * @group dev
 * @group travis
 */
class AppControllerRoutingTest extends RoutingTestCase{

    public function getControllerClass(){
        return 'AppBundle\Controller\AppController';
    }

    public function getExcludedRoutes()
    {
        return array();
    }

    public function getParameters()
    {
        return array();
    }

}