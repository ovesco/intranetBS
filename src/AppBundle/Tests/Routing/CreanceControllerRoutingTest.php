<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 23.11.15
 * Time: 14:31
 */

namespace AppBundle\Tests\Routing;

use AppBundle\Utils\ListUtils\ListKey;

/**
 * @group routing
 * @group dev
 * @group travis
 */
class CreanceControllerRoutingTest extends RoutingTestCase{

    public function getControllerClass(){
        return 'AppBundle\Controller\CreanceController';
    }

    public function getParameters()
    {
        return array(
            'app_creance_remove'=>array('creance'=>1),
            'app_creance_create'=>array('debiteur'=>2),
            'app_creance_show'=>array('creance'=>3),
            'app_creance_facturation'=>array('list_session_key'=>ListKey::CREANCES_SEARCH_RESULTS),
        );
    }

}