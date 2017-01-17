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
class MembreControllerRoutingTest extends RoutingTestCase{

    public function getControllerClass(){
        return 'AppBundle\Controller\MembreController';
    }

    public function getExcludedRoutes()
    {
        return array(
            'app_membre_getmembreproperty',
            'app_membre_topdf',
            'app_membre_ajax_remove_attr_dist',
            'app_membre_ajax_verify_numero_bs',
            'app_membre_modify_famille'
        );
    }

    public function getParameters()
    {

        $membres = $this->container->get('app.repository.membre')->findRandom(1);

        return array(
            'app_membre_show'=>array('membre'=>$membres[0]->getId()),
        );
    }

}