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
 */
class AppControllerRoutingTest extends PageTestCase{

    public function getControllerClass(){
        return 'AppBundle\Controller\AppController';
    }

}