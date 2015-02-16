<?php

namespace Interne\SecurityBundle\Voters;

use Interne\SecurityBundle\Utils\RolesUtil;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Doctrine\ORM\EntityManager;

class RoleHierarchyVoter extends RoleVoter {

    private $em;

    public function __construct(EntityManager $em) {

        $this->em = $em;
        parent::__construct();
    }
    /**
     * {@inheritdoc}
     */
    protected function extractRoles(TokenInterface $token) {

        $roles    = $token->getRoles();
        $corrects = array();

        foreach($roles as $role)
            $corrects = array_merge($corrects, $this->em->getRepository('InterneSecurityBundle:Role')->find($role->getId())->getEnfantsRecursive(true));

        return self::removeDoublons($corrects);
    }

    /**
     * Supprimme les doublons parmi les roles passés en paramètre
     * @param array $roles
     * @return array
     */
    private static function removeDoublons(array $roles) {

        $returned = array();

        foreach($roles as $r)
            if(!in_array($r, $returned))
                $returned[] = $r;

        return $returned;
    }
}