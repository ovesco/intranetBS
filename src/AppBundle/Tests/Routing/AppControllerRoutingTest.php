<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 23.11.15
 * Time: 14:31
 */

namespace AppBundle\Tests\Routing;

/**
 * @group app_bundle
 * @group routing
 * @group routing_app_bundle
 * @group routing_app_controller
 */
class AppControllerRoutingTest extends RoutingTester{

    public function getRoutes(){

        return array(
           // array(RoutingTester::APP_PREFIX), todo
            array(RoutingTester::APP_PREFIX.'/test'),
        );

    }

}