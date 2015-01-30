<?php

namespace Interne\SecurityBundle\Securer\Ressource;
use Doctrine\ORM\EntityManager;
use Interne\SecurityBundle\Securer\Exceptions\IncorrectActionException;
use Symfony\Component\Security\Core\SecurityContext;
use Interne\SecurityBundle\Securer\Exceptions\RessourceLockedException;

/**
 * Centre de la sécurité des ressources. Le système récupère des informations, appelle ensuite le vérificateur
 * correspondant
 * @package Interne\SecurityBundle\Securer
 */
class CoreRessourceSecurer {

    private $context;
    private $em;
    private $params;

    public function __construct(EntityManager $em, SecurityContext $context, $params) {

        $this->context = $context;
        $this->em      = $em;
        $this->params  = $params['ressource_securer'];
    }


    /**
     * Méthode principale de vérification
     * @param string $action
     * @param object $ressource
     * @param boolean $exceptionThrown si on veut récupérer un boolean ou balancer direct l'exception
     * @return boolean
     * @throws RessourceLockedException
     * @throws IncorrectActionException
     */
    public function grantable($action, $ressource, $exceptionThrown = false) {

        if(is_null($type) || !in_array($type, $this->params['allowed_actions'])) throw new IncorrectActionException($action, $this->params);

        $user   = $this->context->getToken()->getUser();

        //On analyse ensuite la ressource passée en paramètre
        $classe = explode('\\', \Doctrine\Common\Util\ClassUtils::getRealClass(get_class($ressource)));
        $classe = $classe[count($classe)-1];

        $verificateur = $this->getVerificateur($classe);

        if($verificateur->verify($ressource, $action, $user)) return true;
        else if(!$exceptionThrown) return false;
        else throw $this->buildException($action, $ressource);
    }

    /**
     * Génère une RessourceLockedException
     * @param string $action
     * @param object $ressource
     * @return RessourceLockedException
     */
    private function buildException($action, $ressource) {

        $exception = new RessourceLockedException("Vous n'avez pas les accès requis pour accéder à la ressource souhaitée");
        $exception->action = $action;
        $exception->ressource = $ressource;

        return $exception;
    }

    /**
     * Fournis le verificateur correspondant à la ressource que l'on souhaite vérifier
     * @param $classe
     */
    private function getVerificateur($classe) {

        $namespace = 'Interne\\SecurityBundle\\Securer\\Ressource\\Verificateurs\\' . $classe . "Verificateur";
        return new $namespace();
    }
}