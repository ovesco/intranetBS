<?php

namespace Interne\SecurityBundle\Securer\Ressource\Verificateurs;

use Doctrine\Common\Collections\ArrayCollection;

class MembreVerificateur extends DefaultVerificateur {

    /**
     * @return bool
     */
    public function verify() {

        /** @var \AppBundle\Entity\Membre $membre */
        $membre = $this->ressource;
        $user   = $this->context->getToken()->getUser();
        $roles  = $this->getConcernedRoles($user->getAllRoles(), "MEMBRE");

        /*
         * On itère ensuite sur l'ensemble des attributions actuelles de la ressource membre pour déterminer le role
         * minimum a avoir pour pouvoir accéder à la ressource
         */
        $ressourceAttr = $membre->getActiveAttributions();
        $userAttr      = $user->getMembre()->getActiveAttributions();
        $requiredRoles = array();

        foreach($userAttr as $Uattr) {
            foreach($ressourceAttr as $Rattr) {

                $uGroupe = $Uattr->getGroupe();
                $rGroupe = $Rattr->getGroupe();

                /*
                 * On va ensuite réaliser une série de tests simples pour déterminer si le groupe respecte les portées
                 * self, groupe, parent, all. A partir du moment où un test est respecté, on génère le role correspondant
                 * et on regarde si il correspond avec un de ceux qu'on a dans $roles. Si c'est le cas, la ressource est
                 * accessible, sinon on continue
                 */
                $generatedRole = "ROLE_MEMBRE_";

                if($uGroupe == $rGroupe) $generatedRole .= "SELF";
                else if(in_array($rGroupe, $uGroupe->getEnfantsRecursive(true))) $generatedRole .= "GROUPE";
                else if(in_array($rGroupe, $uGroupe->getParent()->getEnfantsRecursive(true))) $generatedRole .= "PARENT";
                else $generatedRole .= "ALL";

                $generatedRole .= "_" . $this->translateAction();

                if(in_array($generatedRole, $roles)) return true;
            }
        }

        return false;
    }
}