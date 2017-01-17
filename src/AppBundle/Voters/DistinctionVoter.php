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

class DistinctionVoter extends BaseVoter{


    protected function getSupportedClass()
    {
        return 'AppBundle\Entity\Distinction';
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
        return $this->hasRole('ROLE_DISTINCTION_EDIT',$token);
    }

    /**
     * {@inheritdoc}
     */
    protected function canRemove($subject, User $user, TokenInterface $token)
    {
        return $this->hasRole('ROLE_DISTINCTION_REMOVE',$token);
    }

    /**
     * {@inheritdoc}
     */
    protected function canCreate($subject, User $user, TokenInterface $token)
    {
        return $this->hasRole('ROLE_DISTINCTION_CREATE',$token);
    }

}