<?php

namespace Interne\SecurityBundle\Securer\Ressource\Verificateurs;

use Doctrine\Common\Collections\ArrayCollection;

class FamilleVerificateur extends DefaultVerificateur {

    /**
     * @return bool
     */
    public function verify() {

        /** @var \AppBundle\Entity\Membre $membre */
        $rFamille = $this->ressource;
        $user     = $this->context->getToken()->getUser();
        $roles    = $this->getConcernedRoles($user->getAllRoles(), "FAMILLE");


        /*
         * Pour analyser si un utilisateur a le droit d'accéder à une famille, il faut pour cela qu'au moins 1 membre
         * de cette famille respecte la condition de portée. Ca revient à faire un test de ressource membre sur chaque
         * membre de la famille
         */
        $membres = $rFamille->getMembres();

        foreach($user->getMembre()->getActiveAttributions() as $Uattr) {

            $uGroupe = $Uattr->getGroupe();

            /** @var \AppBundle\Entity\Membre $membre_famille */
            foreach ($membres as $membre_famille) {

                $attrs = $membre_famille->getActiveAttributions();

                /** @var \AppBundle\Entity\Attribution $rAttr */
                foreach ($attrs as $rAttr) {

                    $rGroupe = $rAttr->getGroupe();

                    $generatedRole = "ROLE_FAMILLE_";

                    if($uGroupe == $rGroupe) $generatedRole .= "SELF";
                    else if(in_array($rGroupe, $uGroupe->getEnfantsRecursive(true))) $generatedRole .= "GROUPE";
                    else if(in_array($rGroupe, $uGroupe->getParent()->getEnfantsRecursive(true))) $generatedRole .= "PARENT";
                    else $generatedRole .= "ALL";

                    $generatedRole .= "_" . $this->translateAction();


                    if(in_array($generatedRole, $roles)) return true;
                }
            }
        }

        return false;
    }
}