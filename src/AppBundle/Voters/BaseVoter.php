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

abstract class BaseVoter extends  Voter {

    const VIEW = 'view';
    const CREATE = 'create';
    const EDIT = 'edit';
    const REMOVE = 'remove';

    /** @var  AccessDecisionManagerInterface */
    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function hasRole($role,$token)
    {
        if ($this->decisionManager->decide($token, array($role))) {
            return true;
        }
        return false;
    }

    abstract protected function getSupportedClass();


    protected function supports($attribute, $subject){

        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::VIEW, self::EDIT,self::CREATE,self::REMOVE))) {
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
            case self::VIEW:
                return $this->canView($subject, $user, $token);
            case self::EDIT:
                return $this->canEdit($subject, $user, $token);
            case self::REMOVE:
                return $this->canRemove($subject, $user, $token);
            case self::CREATE:
                return $this->canCreate($subject, $user, $token);
        }

        throw new \LogicException('This code should not be reached!');
    }




    /**
     * @param $subject
     * @param User $user
     * @param TokenInterface $token
     * @return boolean
     */
    abstract protected function canView($subject, User $user, TokenInterface $token);

    /**
     * @param $subject
     * @param User $user
     * @param TokenInterface $token
     * @return boolean
     */
    abstract protected function canEdit($subject, User $user, TokenInterface $token);

    /**
     * @param $subject
     * @param User $user
     * @param TokenInterface $token
     * @return boolean
     */
    abstract protected function canRemove($subject, User $user, TokenInterface $token);

    /**
     * @param $subject
     * @param User $user
     * @param TokenInterface $token
     * @return boolean
     */
    abstract protected function canCreate($subject, User $user, TokenInterface $token);

}


