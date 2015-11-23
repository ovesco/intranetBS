<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 23.11.15
 * Time: 14:31
 */

namespace AppBundle\Tests\Routing;

use AppBundle\Entity\Groupe;
use AppBundle\Entity\Membre;
use AppBundle\Entity\Personne;
use AppBundle\Entity\Famille;

/**
 * @group app_bundle
 * @group routing
 * @group routing_app_bundle
 * @group routing_membre_controller
 */
class MembreControllerRoutingTest extends RoutingTester{

    const CONTROLLER_PATH = '/membre';

    public function getRoutes(){

        $membre = new Membre();

        $membre->setPrenom('Test');
        $membre->setSexe(Personne::FEMME);
        $membre->setNaissance(new \DateTime());
        $membre->setInscription(new \DateTime());
        $membre->setValidity(0);
        $membre->setEnvoiFacture('Famille');



        $famille = new Famille();
        $famille->setNom('TEST');
        $membre->setFamille($famille);

        $this->em->persist($membre);
        $this->em->flush();

        $id = $membre->getId();

        return array(
            array(RoutingTester::APP_PREFIX.MembreControllerRoutingTest::CONTROLLER_PATH.'/add'),
            array(RoutingTester::APP_PREFIX.MembreControllerRoutingTest::CONTROLLER_PATH.'/show/'.$id),
        );

    }

}