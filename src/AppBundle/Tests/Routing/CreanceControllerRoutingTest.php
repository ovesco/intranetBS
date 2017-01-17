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

    public function getExcludedRoutes()
    {
        return array('app_creance_remove');
    }

    public function getParameters()
    {

        $creances = $this->container->get('app.repository.creance')->findRandom(1);
        $debiteur = $this->container->get('app.repository.debiteur')->findRandom(1);

        return array(
            'app_creance_create'=>array('debiteur'=>$debiteur[0]->getId()),
            'app_creance_show'=>array('creance'=>$creances[0]->getId()),
            'app_creance_facturation'=>array('list_session_key'=>ListKey::CREANCES_SEARCH_RESULTS),
        );
    }

}