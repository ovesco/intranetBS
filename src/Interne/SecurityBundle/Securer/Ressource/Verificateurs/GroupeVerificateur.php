<?php

namespace Interne\SecurityBundle\Securer\Ressource\Verificateurs;

use Doctrine\Common\Collections\ArrayCollection;

class GroupeVerificateur extends DefaultVerificateur {

    /**
     * @return bool
     */
    public function verify() {

        /** @var \AppBundle\Entity\Membre $membre */
        $rGroupe = $this->ressource;
        $user    = $this->context->getToken()->getUser();
        $roles   = $this->getConcernedRoles($user->getAllRoles(), "GROUPE");

        /*
         * On analyse ensuite le groupe ressource pour déterminer quel role minimum il faut pour pouvoir y accéder
         * avec l'action souhaitée, en itérant sur chaque attribution du membre
         */
        $userAttr      = $user->getMembre()->getActiveAttributions();

        /** @var \AppBundle\Entity\Attribution $Uattr */
        foreach($userAttr as $Uattr) {

            $uGroupe = $Uattr->getGroupe();

            //echo 'Ressource : ' . $rGroupe->getNom() . " - UserGroupe : " . $uGroupe->getNom();

            $generatedRole = "ROLE_GROUPE_";

            if($uGroupe == $rGroupe) $generatedRole .= "SELF";
            else if(in_array($rGroupe, $uGroupe->getParent()->getEnfantsRecursive(true))) $generatedRole .= "PARENT";
            else $generatedRole .= "ALL";

            $generatedRole .= "_" . $this->translateAction();


            if(in_array($generatedRole, $roles)) return true;
        }

        return false;
    }
}