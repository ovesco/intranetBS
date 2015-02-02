<?php

namespace Interne\SecurityBundle\Securer\Ressource\Verificateurs;

use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class DefaultVerificateur
 * Classe doit être parente de tout vérificateur potentiel
 * @package Interne\SecurityBundle\Securer\Ressource\Verificateurs
 */
abstract class DefaultVerificateur {

    /**
     * Ressource à vérifier (entité)
     * @var mixed
     */
    protected $ressource;

    /**
     * Action souhaitée
     * @var string
     */
    protected $action;

    /**
     * Contexte de sécurité
     * @var SecurityContext
     */
    protected $context;

    /**
     * Les paramètres
     * @var array
     */
    protected $params;


    public function __construct($ressource, $action, $context, $params) {

        $this->ressource = $ressource;
        $this->context   = $context;
        $this->action    = $action;
        $this->params    = $params;
    }

    /**
     * Retourne l'action du role correspondante à l'action que l'on a donné au verificateur lors de l'instanciation
     * @return string
     */
    protected function translateAction() {

        switch($this->action) {

            case 'view':return 'VIEW';
            case 'modif':return 'MODIF';
            case 'remove':return 'REMOVE';
            case 'add':return 'ADD';
        }
    }

    /**
     * Retourne la liste des rôles concernés par l'action souhaitée parmi la liste de rôles passés
     * en paramètre
     * @param array $roles
     * @param string $roleAction
     * @return array
     */
    protected function getConcernedRoles($roles, $roleAction) {

        $returned = array();

        foreach($roles as $r) {

            $data = explode('_', $r->getRole());
            if($data[1] == $roleAction && $data[3] == $this->translateAction()) $returned[] = $r->getRole();
        }

        return $returned;
    }

    /**
     * Retourne le role parent à tous les roles dans le tableau. Pour ce faire on se base sur la logique de portée,
     * en suivant les mots clés définis dans les paramètres
     * @param ArrayCollection $roles
     * @return Role
     */
    protected function getMainParent($roles) {

        $main = null;
        $params = array_reverse($this->params['portee']);

        foreach($params as $p) {
            foreach ($roles as $r) {

                $data = explode('_', $r->getRole());

                if($data[2] == $p)
                    $main = $r;
            }
        }

        return $main;
    }

    /**
     * Méthode obligatoire
     * Appelée par le RessourceCore, elle doit définir si la ressource est accessible ou non à l'utilisateur authentifié
     * dans le contexte de sécurité
     * @return boolean
     */
    abstract function verify();
}