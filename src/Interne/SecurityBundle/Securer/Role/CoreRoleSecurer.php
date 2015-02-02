<?php

namespace Interne\SecurityBundle\Securer\Role;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Centre de la sécurité par filtrage de role. Celle-ci comprend la sécurité par roles direct. Elle gère ainsi la limitation par
 * un ou plusieurs roles
 */
class CoreRoleSecurer {

    private $context;

    public function __construct(SecurityContextInterface $context) {

        $this->context = $context;
    }

    /**
     * Vérifie que l'utilisateur courant ait TOUS les roles passés en paramètres
     * @param array $roles
     * @return boolean
     */
    public function hasRoles(array $roles) {

        foreach($roles as $r) {
            if (!$this->hasRole($r)) return false;
        }

        return true;
    }

    /**
     * hasRole vérifie si l'utilisateur courant possède le role passé en paramètre
     * @param string $role
     * @return boolean
     */
    public function hasRole($role) {

        $roles = $this->context->getToken()->getUser()->getAllRoles();
        foreach($roles as $r)
            if($r->getRole() == $role) return true;

        return false;
    }
}