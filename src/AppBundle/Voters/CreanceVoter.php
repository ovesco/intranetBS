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

class CreanceVoter extends BaseVoter{


    protected function getSupportedClass()
    {
        return 'AppBundle\Entity\Creance';
    }

    /**
     * {@inheritdoc}
     */
    protected function canRead($subject, User $user, TokenInterface $token)
    {
        return $this->hasRole('ROLE_CREANCE_READ',$token);
    }

    /**
     * {@inheritdoc}
     */
    protected function canUpdate($subject, User $user, TokenInterface $token)
    {
        return $this->hasRole('ROLE_CREANCE_UPDATE',$token);
    }

    /**
     * {@inheritdoc}
     */
    protected function canDelete($subject, User $user, TokenInterface $token)
    {
        return $this->hasRole('ROLE_CREANCE_DELETE',$token);
    }

    /**
     * {@inheritdoc}
     */
    protected function canCreate($subject, User $user, TokenInterface $token)
    {
        return $this->hasRole('ROLE_CREANCE_CREATE',$token);
    }

}