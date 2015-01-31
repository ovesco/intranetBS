<?php

namespace Interne\SecurityBundle\Securer;

use Interne\SecurityBundle\Securer\Ressource\CoreRessourceSecurer;
use Interne\SecurityBundle\Securer\Role\CoreRoleSecurer;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
/**
 * Class Securer
 * C'est un service, donc on peut l'utiliser n'importe où pour sécuriser quelque chose. Il donne accès à la sécurisation
 * par role et la sécurité des ressources
 * @package Interne\SecurityBundle\Securer
 */
class Securer {

    private $coreRessource;
    private $coreRole;

    public function __construct(CoreRessourceSecurer $coreRessourceSecurer, CoreRoleSecurer $coreRoleSecurer) {

        $this->coreRessource = $coreRessourceSecurer;
        $this->coreRole      = $coreRoleSecurer;
    }

    /**
     * Vérifie si l'utilisateur courant possède le role passé en paramètre
     * Raccourci de RoleCore::hasRole
     * @param  string
     * @param boolean $throwException
     * @return boolean
     * @throws AccessDeniedException
     */
    public function hasRole($role, $throwException = false) {

        if($this->coreRole->hasRole($role)) return true;
        else if($throwException) throw new AccessDeniedException("Accès refusé");
        else return false;
    }

    /**
     * Vérifie si l'utilisateur courant possède le role passé en paramètre
     * Raccourci de RoleCore::hasRoles
     * @param  string|array $roles
     * @param boolean $throwException
     * @return boolean
     * @throws AccessDeniedException
     */
    public function hasRoles($roles, $throwException = false) {

        if(is_string($roles)) {

            $str   = str_replace(' ', '', $roles);
            $roles = explode(',', $str);
        }

        if($this->coreRole->hasRoles($roles)) return true;
        else if($throwException) throw new AccessDeniedException("Accès refusé");
        else return false;
    }

    /**
     * Vérifie si l'utilisateur a accès à la ressource passée en paramètre pour l'action donnée
     * @param string $action
     * @param object $ressource
     * @param boolean $throwException
     * @return boolean
     * @throws \Interne\SecurityBundle\Securer\Exceptions\RessourceLockedException
     */
    public function ressourceAvailable($action, $ressource, $throwException = false) {

        return $this->coreRessource->grantable($action, $ressource, $throwException);
    }
}