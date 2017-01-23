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
class AccountControllerRoutingTest extends RoutingTestCase{

    public function getControllerClass(){
        return 'AppBundle\Controller\AccountController';
    }

    public function getExcludedRoutes()
    {
        return array('app_account_modifypassword');
    }

    public function getParameters()
    {
        return array();
    }

}