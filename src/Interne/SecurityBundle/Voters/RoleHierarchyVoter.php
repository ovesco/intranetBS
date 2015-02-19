<?php

namespace Interne\SecurityBundle\Voters;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Doctrine\ORM\EntityManager;

class RoleHierarchyVoter extends RoleVoter {

    private $em;
    private $session;
    private static $sessionAttribute    = 'user-roles-hierarchies';
    private static $expirationAttribute = 'user-roles-token';
    private static $expirationTime      = 300;

    public function __construct(EntityManager $em, Session $session) {

        $this->em = $em;
        $this->session = $session;

        parent::__construct();
    }
    /**
     * {@inheritdoc}
     */
    protected function extractRoles(TokenInterface $token) {

        /*
         * L'utilisateur peut avoir une pétée de roles
         * De ce fait, on stocke sa hierarchie de roles en session afin d'éviter d'avoir à realiser
         * des requetes doctrines à chaque fois, et on place un token d'expiration dessus pour qu'il la recharge
         * si nécessaire
         */
        $session = $this->session;

        var_dump($session->get(self::$expirationAttribute) > new \Datetime('now'));

        if(!$session->has(self::$sessionAttribute)
            || ($session->get(self::$sessionAttribute) == null)
            || !($session->get(self::$expirationAttribute) > new \Datetime('now'))) {


            /*
             * Sinon on regénère l'arbre des roles et on met à jour le token datetime
             * d'expiration
             */
            $newToken = new \Datetime();
            $newToken->setTimestamp(time() + self::$expirationTime);

            $session->set(self::$sessionAttribute, $this->fetchRoles($token));
            $session->set(self::$expirationAttribute, $newToken);

        }

        return $session->get(self::$sessionAttribute);

    }


    /**
     * Fetch la hierarchie des roles à partir de la BDD
     * @param TokenInterface $token
     * @return array
     */
    private function fetchRoles($token) {

        $roles    = $token->getRoles();

        $corrects = array();

        foreach($roles as $role)
            $corrects = array_merge($corrects, $this->em->getRepository('InterneSecurityBundle:Role')->find($role->getId())->getEnfantsRecursive(true));


        return self::removeDoublons($corrects);
    }

    /**
     * Supprimme les doublons parmi les roles passés en paramètre
     * @param array $roles
     * @return array
     */
    private static function removeDoublons(array $roles) {

        $returned = array();

        foreach($roles as $r)
            if(!in_array($r, $returned))
                $returned[] = $r;

        return $returned;
    }
}