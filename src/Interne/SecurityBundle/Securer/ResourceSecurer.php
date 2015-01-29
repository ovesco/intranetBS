<?php

namespace Interne\SecurityBundle\Securer;

use Interne\SecurityBundle\Entity\User;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Permet de sécuriser une ressource en fonction du membre qui souhaite y accéder
 *
 * Cette méthode fonctionne en 2 étapes. La première, consiste en la vérification simple.
 *
 * @package Interne\SecurityBundle\Securer
 */
class ResourceSecurer {

    private $context;
    private $params;

    public function __construct(SecurityContext $context, $params) {

        $this->context = $context;
        $this->params  = $params['resource_securer'];
    }


    /**
     * Méthode principale qui vérifie si le membre a accès à la ressource passée en paramètre
     * @param object $ressource
     * @param string $type
     * @throws \Exception si l'action souhaitée n'est pas supportée
     */
    public function isAvailableFor($ressource, $type) {

        if(is_null($type) || !in_array($type, $this->params['allowed_actions'])) throw new \Exception("L'action (%s) n'est pas supportée", $type);
        $user = $this->context->getToken()->getUser();

        //On analyse ensuite la ressource passée en paramètre
        $classe = explode('\\', \Doctrine\Common\Util\ClassUtils::getRealClass(get_class($ressource)));
        $classe = $classe[count($classe)-1];

        /*
         * A partir d'ici on va pouvoir génerer
         */
    }
}