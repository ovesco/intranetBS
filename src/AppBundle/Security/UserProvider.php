<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 21.10.16
 * Time: 11:53
 */

namespace AppBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use AppBundle\Security\RoleHierarchy;


class UserProvider implements UserProviderInterface {

    /** @var UserRepository  */
    private $repository;

    /** @var  RoleHierarchy */
    private $roleHierarchy;

    public function __construct(UserRepository $repository, RoleHierarchy $hierarchy){
        $this->repository = $repository;
        $this->roleHierarchy = $hierarchy;
    }

    public function loadUserByUsername($username)
    {

        $user = $this->repository->loadUserByUsername($username);

        if($user instanceof User)
        {
            $user->setRoles(
                $this->roleHierarchy->getDeducedRoles(
                    array_merge($user->getSelectedRoles(),$user->getMembreRoles())
                ));

            return $user;
        }



        throw new UsernameNotFoundException(
            sprintf('Username "%s" does not exist.', $username)
        );
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'AppBundle\Entity\User';
    }

}