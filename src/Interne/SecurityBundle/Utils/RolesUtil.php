<?php

namespace Interne\SecurityBundle\Utils;
use AppBundle\Entity\Attribution;
use Interne\SecurityBundle\Entity\Role;

/**
 * Fournit diverses méthodes utiles pour la gestion des roles
 */

class RolesUtil {

    /**
     * Supprimme les doublons parmi les roles passés en paramètre
     * @param array $roles
     * @return array
     */
    public function removeDoublons(array $roles) {

        $returned = array();

        foreach($roles as $r)
            if(!in_array($r, $returned))
                $returned[] = $r;

        return $returned;
    }

    /**
     * Prend des roles en paramètre, et les formatte pour go.js
     * @param $roles
     * @return array
     */
    public function rolesToGOJS($roles) {

        $return = array();

        /** @var Role $r */
        foreach($roles as $r)
            $return[] = array(

                'key'       => $r->getId(),
                'parent'    => ($r->getParent() == null) ? 0 : $r->getParent()->getId(),
                'nom'       => $r->getName(),
                'role'      => $r->getRole()
            );

        return $return;
    }
}