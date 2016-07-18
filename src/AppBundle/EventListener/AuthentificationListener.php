<?php

namespace AppBundle\EventListener;

use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class AuthentificationListener {

    private $user;
    private $em;
    private $listing;

    public function __construct($token, $em, $listing) {

        $this->user     = ($token->getToken()->getUser() == null) ? null : $token->getToken()->getUser();
        $this->em       = $em;
        $this->listing  = $listing;
    }

    /**
     * onAuthenticationSuccess
     * @param InteractiveLoginEvent $event
     */
    public function onAuthenticationSuccess( InteractiveLoginEvent $event )
    {
        $user = $this->user;

        // On crée une liste vide dans le listing parce que Muller
        /*
         * todo donner un meilleure raison que ca pour ce bout de code
         * a terme, on devrais récupéré les listes pour que elle
         * reste de session en ession
         */
        /** @var \AppBundle\Utils\Listing\Lister $listing */
        $listing = $this->listing;


        if($listing->get('Ma super liste') == null) {

            $listing->addListe('Ma super liste');
            $listing->save();
        }



        $user->setLastConnexion(new \Datetime());
        $this->em->persist($user);
        $this->em->flush();
    }
}