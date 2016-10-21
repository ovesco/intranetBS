<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 21.10.16
 * Time: 10:31
 */

namespace AppBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;

class AuthentificationSubscriber implements EventSubscriberInterface{

    /** @var  UserRepository */
    private $userRepository;

    /**
     * @param UserRepository $user_repository
     */
    public function __construct(UserRepository $user_repository)
    {
        $this->userRepository = $user_repository;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            SecurityEvents::INTERACTIVE_LOGIN => array('onSecurityInteractiveLogin',10),//le nombre c'est la priorité d'execution
        );
    }

    /**
     * event: security.interactive_login
     *
     * The security.interactive_login event is triggered after a user has actively logged into your website.
     *
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();

        if($user != null)
        {
            // On crée une liste vide dans le listing parce que Muller
            /*
             * todo CRM or GHT donner un meilleure raison que ca pour ce bout de code et injecter le service listing si necassaire
             * a terme, on devrais récupéré les listes pour que elle
             * reste de session en ession
             */
            //** @var \AppBundle\Utils\Listing\Lister $listing */
            //$listing = $this->listing;


            /*
            if($listing->get('Ma super liste') == null) {

                $listing->addListe('Ma super liste');
                $listing->save();
            }
            */

            $user->setLastConnexion(new \Datetime('now'));

            $this->userRepository->save($user);

        }


    }

}