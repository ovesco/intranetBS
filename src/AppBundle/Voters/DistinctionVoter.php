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
    protected function canRead($subject, User $user, TokenInterface $token)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function canUpdate($subject, User $user, TokenInterface $token)
    {
        return $this->hasRole('ROLE_DISTINCTION_UPDATE',$token);
    }

    /**
     * {@inheritdoc}
     */
    protected function canDelete($subject, User $user, TokenInterface $token)
    {
        return $this->hasRole('ROLE_DISTINCTION_DELETE',$token);
    }

    /**
     * {@inheritdoc}
     */
    protected function canCreate($subject, User $user, TokenInterface $token)
    {
        return $this->hasRole('ROLE_DISTINCTION_CREATE',$token);
    }

}