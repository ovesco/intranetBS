<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 23.11.15
 * Time: 14:31
 */

namespace AppBundle\Tests\Routing;

use AppBundle\Entity\Categorie;
/**
 * @group app_bundle
 * @group routing
 * @group routing_app_bundle
 * @group routing_categorie_controller
 */
class CategorieControllerRoutingTest extends RoutingTester{

    const CONTROLLER_PATH = '/categorie';

    public function getRoutes(){

        $categorie = new Categorie();
        $categorie->setNom('Test');
        $categorie->setDescription('categorie de test');

        $this->em->persist($categorie);
        $this->em->flush();

        $id = $categorie->getId();

        return array(
            array(RoutingTester::APP_PREFIX.CategorieControllerRoutingTest::CONTROLLER_PATH.'/list'),
            array(RoutingTester::APP_PREFIX.CategorieControllerRoutingTest::CONTROLLER_PATH.'/add'),
            array(RoutingTester::APP_PREFIX.CategorieControllerRoutingTest::CONTROLLER_PATH.'/edit/'.$id),
            array(RoutingTester::APP_PREFIX.CategorieControllerRoutingTest::CONTROLLER_PATH.'/remove/'.$id),
        );

    }

}