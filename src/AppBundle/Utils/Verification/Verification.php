<?php

namespace AppBundle\Utils\Verification;

use Doctrine\ORM\EntityManager;

/**
 * Class Verification
 * Fournis les méthodes utiles à la gestion de la verification de la modification des données du système
 * @package AppBundle\Utils\Verification
 */
class Verification {

    private $em;

    public function __construct(EntityManager $entityManager) {

        $this->em = $entityManager;
    }


}