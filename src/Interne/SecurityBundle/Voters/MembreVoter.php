<?php

namespace Interne\SecurityBundle\Voters;

use Interne\SecurityBundle\Voters\Abstracts\StructureVoter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Interne\SecurityBundle\Utils\RolesUtil;

class MembreVoter extends StructureVoter
{

    public function supportsClass($class)
    {
        return $class == 'AppBundle\Entity\Membre';
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

            $role = 'ROLE_' . strtoupper($attribute) . '_MEMBRE';

            //On autorise a regarder sa propre fiche
            if($attribute == 'view' && $user->getMembre() == $object)
                return $this::ACCESS_GRANTED;

            if(!in_array($role, $roles))
                return $this::ACCESS_DENIED;

            /*
             *Si il possède le role requis, on vérifie ensuite si le membre auquel il souhaite accéder possède au
             * moins une attribution avec un groupe en commun avec le user
             */
            foreach($object->getActiveAttributions() as $oAttr)
                foreach($user->getMembre()->getActiveAttributions() as $uAttr)
                    if(in_array($oAttr->getGroupe(), $uAttr->getGroupe()->getEnfantsRecursive(true)))
                        return $this::ACCESS_GRANTED;

            return $this::ACCESS_DENIED;
        }
    }
}