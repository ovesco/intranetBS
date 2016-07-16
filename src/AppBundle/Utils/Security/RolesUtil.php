<?php

namespace AppBundle\Utils\Security;

use AppBundle\Entity\Role;

/**
 * Fournit diverses méthodes utiles pour la gestion des roles
 */

class RolesUtil {

    /**
     * Récupère la totalité des roles parmi ceux passés en paramètres, c'est-à-dire
     * en suivant la hierarchie
     */
    public function getAllRoles(array $roles) {

        $return = array();
        foreach($roles as $role)
            $return = array_merge($return, $role->getEnfantsRecursive(true));

        return self::removeDoublons($return);
    }

    /**
     * Supprimme les doublons parmi les roles passés en paramètre
     * @param array $roles
     * @return array
     */
    public static function removeDoublons(array $roles) {

        $returned = array();

        foreach($roles as $r)
            if(!in_array($r, $returned))
                $returned[] = $r;

        return $returned;
    }

    /**
     * Retourne un array de roles string a partir de roles objet
     */
    public function getRolesAsString(array $roles) {

        $r = array();

        foreach($roles as $role)
            $r[] = $role->getRole();

        return $r;
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