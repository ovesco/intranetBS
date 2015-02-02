<?php

namespace Interne\SecurityBundle\Securer\Twig;

use Interne\SecurityBundle\Securer\Ressource\CoreRessourceSecurer;
use Interne\SecurityBundle\Securer\Role\CoreRoleSecurer;

class SecurerExtension extends \Twig_Extension
{
    private $ressourceCore;
    private $roleCore;

    public function __construct(CoreRessourceSecurer $coreRessource, CoreRoleSecurer $coreRole) {

        $this->ressourceCore = $coreRessource;
        $this->roleCore      = $coreRole;
    }

    public function getFunctions() {

        return array(
            'ressourceGranted' => new \Twig_Function_Method($this, 'secureRessource'),
            'hasRoles'         => new \Twig_Function_Method($this, 'secureRoles')
        );
    }


    /**
     * Securise une ressource depuis une vue twig
     * @param string $action
     * @param object $ressource
     * @return boolean
     */
    public function secureRessource($action, $ressource) {

        return $this->ressourceCore->grantable($action, $ressource);
    }

    /**
     * Sécurise une ressource en fonction d'une liste de roles depuis twig
     * @param string $roles
     * @return boolean
     */
    public function secureRoles($roles) {

        $roles  = str_replace(' ', '', $roles);
        $roles  = explode(',', $roles);

        return $this->roleCore->hasRoles($roles);
    }

    public function getName()
    {
        return 'secure_ressource_extension';
    }
}