<?php

namespace Interne\SecurityBundle\Voters;

use Interne\SecurityBundle\Utils\RolesUtil;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Doctrine\ORM\EntityManager;

class RoleHierarchyVoter extends RoleVoter {

    public function __construct($prefix = 'ROLE_') {

        parent::__construct($prefix);
    }

    /**
     * {@inheritdoc}
     */
    protected function extractRoles(TokenInterface $token) {

        $util  = new RolesUtil();
        $roles = $util->removeDoublons($util->getAllRoles($token->getUser()->getRoles()));

        return $roles;
    }
}