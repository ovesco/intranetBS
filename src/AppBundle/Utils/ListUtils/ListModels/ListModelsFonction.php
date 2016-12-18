<?php

namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\ActionList;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModelInterface;
use AppBundle\Utils\ListUtils\ListRenderer;
use Symfony\Component\Routing\Router;
use AppBundle\Entity\Fonction;


class ListModelsFonction implements ListModelInterface
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
        $list->setName('fonction_default');

        //$list->setSearchBar(true);

        $list->addColumn(new Column('Nom', function (Fonction $fonction) {
            return $fonction->getNom();
        }));

        $list->addColumn(new Column('Abréviation', function (Fonction $fonction) {
            return $fonction->getAbreviation();
        }));

        $list->addColumn(new Column('Roles', function (Fonction $fonction) {
            //todo NUR v2 mettre en place les roles par fonctions
            return '<div class="ui red basic label">Prévu pour la version 2</div>';
            /*
            $roles = '';
            foreach($fonction->getRoles() as $role)
            {
                $roles = $roles.$role.'<br>';
            }
            return $roles;
            */
        }));


        $parameters = function (Fonction $fonction) {
            return array(
                "fonction" => $fonction->getId()
            );
        };

        /* Editer la fonction courant */
        $edit = new ActionLine('Modifier', 'edit', 'app_fonction_edit', $parameters, EventPostAction::ShowModal);
        $edit->setInMass(false);
        $list->addActionLine($edit);

        $delete = new ActionLine('Supprimer', 'remove', 'app_fonction_remove', $parameters, EventPostAction::RefreshPage);
        $delete->setInMass(false);
        $delete->setCondition(function(Fonction $fonction){return $fonction->isRemovable();});
        $list->addActionLine($delete);


        $list->addActionList(new ActionList('Ajouter', 'add', 'app_fonction_add', function(){return array();}, EventPostAction::ShowModal,null,'green'));


        return $list;
    }

}


?>