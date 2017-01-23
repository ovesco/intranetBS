<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 21.10.16
 * Time: 13:31
 */

namespace AppBundle\Voters;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * Class CRUD
 * @package AppBundle\Voters
 *
 * According to standard CRUD
 * https://en.wikipedia.org/wiki/Create,_read,_update_and_delete
 *
 */
class CRUD{
    const CREATE = 'create';
    const READ = 'read';
    const UPDATE = 'update';
    const DELETE = 'delete';
}


abstract class BaseVoter extends  Voter {


    /** @var  AccessDecisionManagerInterface */
    private $decisionManager;

    /**
     * @param AccessDecisionManagerInterface $decisionManager
     */
    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    /**
     * @param $role
     * @param $token
     * @return bool
     */
    protected function hasRole($role,$token)
    {
        if ($this->decisionManager->decide($token, array($role))) {
            return true;
        }
        return false;
    }




    protected function supports($attribute, $subject){

        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(CRUD::CREATE, CRUD::READ,CRUD::UPDATE,CRUD::DELETE))) {
            return false;
        }

        // only vote on supportedClass objects inside this voter
        if (get_class($subject) != $this->getSupportedClass()) {
            return false;
        }

        return true;

    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        switch ($attribute) {
            case CRUD::CREATE:
                return $this->canCreate($subject, $user, $token);
            case CRUD::READ:
                return $this->canRead($subject, $user, $token);
            case CRUD::UPDATE:
                return $this->canUpdate($subject, $user, $token);
            case CRUD::DELETE:
                return $this->canDelete($subject, $user, $token);
        }

        throw new \LogicException('This code should not be reached!');
    }


    /**
     *
     * @return mixed
     */
    abstract protected function getSupportedClass();

    /**
     * @param $subject
     * @param User $user
     * @param TokenInterface $token
     * @return boolean
     */
    abstract protected function canCreate($subject, User $user, TokenInterface $token);

    /**
     * @param $subject
     * @param User $user
     * @param TokenInterface $token
     * @return boolean
     */
    abstract protected function canRead($subject, User $user, TokenInterface $token);

    /**
     * @param $subject
     * @param User $user
     * @param TokenInterface $token
     * @return boolean
     */
    abstract protected function canUpdate($subject, User $user, TokenInterface $token);

    /**
     * @param $subject
     * @param User $user
     * @param TokenInterface $token
     * @return boolean
     */
    abstract protected function canDelete($subject, User $user, TokenInterface $token);

}
