<?php

namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\User;
use AppBundle\Entity\Role;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModelInterface;
use AppBundle\Utils\ListUtils\ListRenderer;
use Symfony\Component\Routing\Router;

class ListModelsUser implements ListModelInterface
{

    /**
     * @param \Twig_Environment $twig
     * @param Router $router
     * @param $items
     * @param string $url
     * @return ListRenderer
     */
    static public function getDefault(\Twig_Environment $twig, Router $router, $items, $url = null)
    {
        $list = new ListRenderer($twig, $items);
        $list->setUrl($url);

        $list->setSearchBar(true);

        $list->addColumn(new Column('Utilisateur', function (User $user) use ($router) {
            return '<a href="' . $router->generate('app_user_show', array('user' => $user->getId())) . '">' . $user->getUsername() . '</a>';
        }));

        $list->addColumn(new Column('Dernière connexion', function (User $user) use ($router) {
            return (is_null($user->getLastConnexion())? '-' : $user->getLastConnexion()->format('d/m/Y'));
        }));

        $list->addColumn(new Column('Actif', function (User $user) use ($router) {
            if($user->getIsActive())
            {
                return '<div class="ui green label">oui</div>';
            }
            else
            {
                return '<div class="ui red label">non</div>';
            }
        }));

        $list->addColumn(new Column('Roles choisis', function (User $user) use ($router) {
            $roles = '';
            foreach($user->getSelectedRoles() as $role)
            {
                $roles = $roles.$role.'<br>';
                //$roles = $roles.$role->getRole().'<br>';
            }
            return $roles;
        }));

        $list->addColumn(new Column('Membre lié', function (User $user) use ($router) {
            if(is_null($user->getMembre())){
                return 'Pas de membre';
            }
            else{
                return $user->getMembre()->getPrenom().' '. $user->getMembre()->getNom();
            }
        }));

        $list->addColumn(new Column('Roles via Membre', function (User $user) use ($router) {

            $roles = $user->getMembreRoles();
            if(!empty($roles))
            {
                $roles = '';
                foreach($user->getMembreRoles() as $role)
                {
                    $roles = $roles.$role.'<br>';
                    //$roles = $roles.$role->getRole().'<br>';
                }
                return $roles;
            }
            return 'Aucun roles fournit par le membre';
        }));

        $userParameters = function (User $user) {
            return array(
                "user" => $user->getId()
            );
        };

        $list->addActionLine(new ActionLine('Modifier', 'edit', 'app_user_edit', $userParameters, EventPostAction::Link));

        $list->addActionLine(new ActionLine('Voir', 'zoom', 'app_user_show', $userParameters, EventPostAction::Link));


        //return '<a href="' . $router->generate('app_membre_show', array('membre' => $membre->getId())) . '">' . $membre->getPrenom() . '</a>';


        return $list;
    }

}


?>