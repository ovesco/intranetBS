<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 21.10.16
 * Time: 13:44
 */

namespace AppBundle\Voters;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use AppBundle\Entity\User;

class CategorieVoter extends BaseVoter{


    protected function getSupportedClass()
    {
        return 'AppBundle\Entity\Categorie';
    }

    /**
     * {@inheritdoc}
     */
    protected function canView($subject, User $user, TokenInterface $token)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function canEdit($subject, User $user, TokenInterface $token)
    {
        return $this->hasRole('ROLE_STRUCTURE',$token);
    }

    /**
     * {@inheritdoc}
     */
    protected function canRemove($subject, User $user, TokenInterface $token)
    {
        return $this->hasRole('ROLE_STRUCTURE',$token);
    }

    /**
     * {@inheritdoc}
     */
    protected function canCreate($subject, User $user, TokenInterface $token)
    {
        return $this->hasRole('ROLE_STRUCTURE',$token);
    }

}