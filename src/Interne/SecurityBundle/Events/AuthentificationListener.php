<?php

namespace Interne\SecurityBundle\Events;

use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class AuthentificationListener {

    private $user;
    private $em;

    public function __construct($token, $em) {

        $this->user = ($token->getToken()->getUser() == null) ? null : $token->getToken()->getUser();
        $this->em    = $em;
    }

    /**
     * onAuthenticationSuccess
     * @param InteractiveLoginEvent $event
     */
    public function onAuthenticationSuccess( InteractiveLoginEvent $event )
    {
        $user = $this->user;

        $user->setLastConnexion(new \Datetime());
        $this->em->persist($user);
        $this->em->flush();
    }
}