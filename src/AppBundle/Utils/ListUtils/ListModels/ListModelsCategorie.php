<?php

namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\Categorie;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\AbstractList;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\ActionList;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListRenderer;
use Symfony\Component\Routing\Router;
use AppBundle\Entity\Model;


class ListModelsCategorie extends  AbstractList
{
    public function getDefault( $items, $url = null)
    {
        $this->setItems($items);
        $this->setUrl($url);
        $this->setName('categorie_default');

        $this->addColumn(new Column('Nom', function (Categorie $categorie) {
            return $categorie->getNom();
        }));


        $this->addColumn(new Column('Models', function (Categorie $categorie) {

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


        $this->addColumn(new Column('Description', function (Categorie $categorie) {
            return $categorie->getDescription();
        }));





        $parameters = function (Categorie $categorie) {
            return array(
                "categorie" => $categorie->getId()
            );
        };

        /* Editer la categorie courant */
        $edit = new ActionLine('Modifier', 'edit', 'app_categorie_edit', $parameters, EventPostAction::ShowModal);
        $edit->setInMass(false);
        $this->addActionLine($edit);

        $delete = new ActionLine('Supprimer', 'remove', 'app_categorie_remove', $parameters, EventPostAction::RefreshList);
        $delete->setInMass(false);
        $delete->setCondition(function(Categorie $categorie){return $categorie->isRemovable();});
        $this->addActionLine($delete);


        $this->addActionList(new ActionList('Ajouter', 'add', 'app_categorie_add', function(){return array();}, EventPostAction::ShowModal,null,'green'));

        return $this;
    }

}


?>