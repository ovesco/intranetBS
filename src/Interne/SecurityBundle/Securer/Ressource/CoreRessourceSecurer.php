<?php

namespace Interne\SecurityBundle\Securer\Ressource;
use Doctrine\ORM\EntityManager;
use Interne\SecurityBundle\Securer\Exceptions\IncorrectActionException;
use Interne\SecurityBundle\Securer\Exceptions\UnknownVerificateurException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Interne\SecurityBundle\Securer\Exceptions\RessourceLockedException;

/**
 * Centre de la sécurité des ressources. Le système récupère des informations, appelle ensuite le vérificateur
 * correspondant
 */
class CoreRessourceSecurer {

    private $context;
    private $params;

    public function __construct(SecurityContextInterface $context, $params) {

        $this->context = $context;
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
     * @throws UnknownVerificateurException
     * @throws \Exception
     */
    public function grantable($action, $ressource, $exceptionThrown = false) {

        if(is_null($action) || !in_array($action, $this->params['allowed_actions'])) throw new IncorrectActionException($action, $this->params['allowed_actions']);
        if(is_null($ressource)) throw new \Exception("La ressource souhaitée est null");

        //On analyse ensuite la ressource passée en paramètre
        $classe = explode('\\', \Doctrine\Common\Util\ClassUtils::getRealClass(get_class($ressource)));
        $classe = $classe[count($classe)-1];

        //On génère le vérificateur
        $namespace    = 'Interne\\SecurityBundle\\Securer\\Ressource\\Verificateurs\\' . $classe . "Verificateur";
        if(!class_exists("\\" . $namespace)) throw new UnknownVerificateurException($namespace);

        $verificateur = new $namespace($ressource, $action, $this->context, $this->params);


        if($verificateur->verify()) return true;
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
}