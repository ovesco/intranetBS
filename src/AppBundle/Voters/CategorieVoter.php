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
    protected function canRead($subject, User $user, TokenInterface $token)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function canUpdate($subject, User $user, TokenInterface $token)
    {
        return $this->hasRole('ROLE_CATEGORIE_UPDATE',$token);
    }

    /**
     * {@inheritdoc}
     */
    protected function canDelete($subject, User $user, TokenInterface $token)
    {
        return $this->hasRole('ROLE_CATEGORIE_DELETE',$token);
    }

    /**
     * {@inheritdoc}
     */
    protected function canCreate($subject, User $user, TokenInterface $token)
    {
        return $this->hasRole('ROLE_CATEGORIE_CREATE',$token);
    }

}