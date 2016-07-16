<?php

namespace AppBundle\Voters;

use AppBundle\Voters\Abstracts\StructureVoter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use AppBundle\Utils\Security\RolesUtil;

class GroupeVoter extends StructureVoter
{

    public function supportsClass($class)
    {
        return $class == 'AppBundle\Entity\Groupe';
    }

    function vote(TokenInterface $token, $object, array $attributes)
    {
        if ( !($this->supportsClass(\Doctrine\Common\Util\ClassUtils::getClass($object))) ) {
            return $this::ACCESS_ABSTAIN;
        }

        foreach ($attributes as $attribute) {
            if ( !$this->supportsAttribute($attribute) ) {
                return $this::ACCESS_ABSTAIN;
            }
        }

        $util  = new RolesUtil();
        $user  = $token->getUser();
        $roles = $util->getRolesAsString($util->getAllRoles($user->getRoles()));

        foreach($attributes as $attribute) {

            $role = 'ROLE_' . strtoupper($attribute) . '_GROUPE';

            /*
             * Comme on veut checker un groupe, on vérifie si au moins un des groupe d'attribution du user possède
             * le groupe passé dans sa hierarchie
             */
            if(!in_array($role, $roles))
                return $this::ACCESS_DENIED;


            foreach($user->getMembre()->getActiveAttributions() as $attr)
                if(in_array($object, $attr->getGroupe()->getEnfantsRecursive(true)))
                    return $this::ACCESS_GRANTED;


            return $this::ACCESS_DENIED;
        }
    }
}