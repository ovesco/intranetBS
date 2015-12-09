<?php

namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\Categorie;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\ActionList;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModelInterface;
use AppBundle\Utils\ListUtils\ListRenderer;
use Symfony\Component\Routing\Router;
use AppBundle\Entity\Model;


class ListModelsCategorie implements ListModelInterface
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
        $list->setName('categorie_default');

        //$list->setSearchBar(true);

        $list->addColumn(new Column('Nom', function (Categorie $categorie) {
            return $categorie->getNom();
        }));


        $list->addColumn(new Column('Models', function (Categorie $categorie) {

            if(!$categorie->getModels()->isEmpty())
            {
                $models = '';
                /** @var Model $model */
                foreach($categorie->getModels() as $model)
                {
                    $models = $models.'<div class="ui blue basic label">'.$model->getNom().'</div> ';
                }
                return $models;
            }
            else
            {
                return "Aucun models liés";
            }
        }));


        $list->addColumn(new Column('Description', function (Categorie $categorie) {
            return $categorie->getDescription();
        }));





        $parameters = function (Categorie $categorie) {
            return array(
                "categorie" => $categorie->getId()
            );
        };

        /* Editer la categorie courant */
        $edit = new ActionLine('Modifier', 'edit', 'app_categorie_edit', $parameters, EventPostAction::RefreshList);
        $edit->setInMass(false);
        $list->addActionLine($edit);

        $delete = new ActionLine('Supprimer', 'remove', 'app_categorie_remove', $parameters, EventPostAction::RefreshList);
        $delete->setInMass(false);
        $delete->setCondition(function(Categorie $categorie){return $categorie->isRemovable();});
        $list->addActionLine($delete);


        $list->addActionList(new ActionList('Ajouter', 'add', 'app_categorie_add', function(){return array();}, EventPostAction::ShowModal));

        return $list;
    }

}


?>