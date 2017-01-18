<?php

namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\Model;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\AbstractList;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\ActionList;
use AppBundle\Utils\ListUtils\Column;
use Symfony\Component\Routing\Router;
use AppBundle\Entity\Fonction;
use AppBundle\Entity\Categorie;


class ListModelsModel extends  AbstractList
{

    public function getDefault( $items, $url = null)
    {
        $this->setItems($items);
        $this->setUrl($url);
        $this->setName('model_default');


        $this->addColumn(new Column('Nom', function (Model $model) {
            return $model->getNom();
        }));

        $this->addColumn(new Column('Fonction chef', function (Model $model) {
            return $model->getFonctionChef()->getNom();
        }));

        $this->addColumn(new Column('Fonctions', function (Model $model) {

            if(!$model->getFonctions()->isEmpty())
            {
                $fonctions = '';
                /** @var Fonction $fonction */
                foreach($model->getFonctions() as $fonction)
                {
                    $fonctions = $fonctions.'<div class="ui blue basic label">'.$fonction->getNom().'</div> ';
                }
                return $fonctions;
            }
            else
            {
                return "Aucunes fonctions définies";
            }
        }));

        $this->addColumn(new Column('Catégorie', function (Model $model) {

            if(!$model->getCategories()->isEmpty())
            {
                $categories = '';
                /** @var Categorie $categorie */
                foreach($model->getCategories() as $categorie)
                {
                    $categories = $categories.$categorie->getNom().', ';
                }
                return $categories;
            }
            else
            {
                return "Aucunes catégories définies";
            }
        }));



        $this->addColumn(new Column('Effectifs', function (Model $model) {

            if($model->isAffichageEffectifs()){
                return 'Affichés';
            }
            else{
                return 'caché';
            }
        }));


        $parameters = function (Model $model) {
            return array(
                "model" => $model->getId()
            );
        };
        /* Editer le model courant */
        $edit = new ActionLine('Modifier', 'edit', 'app_model_edit', $parameters, EventPostAction::ShowModal);
        $edit->setInMass(false);
        $this->addActionLine($edit);

        $delete = new ActionLine('Supprimer', 'remove', 'app_model_remove', $parameters, EventPostAction::ShowModal);
        $delete->setInMass(false);
        $delete->setCondition(function(Model $model){return $model->isRemovable();});
        $this->addActionLine($delete);


        $this->addActionList(new ActionList('Ajouter', 'add', 'app_model_add', function(){return array();}, EventPostAction::ShowModal,null,'green'));


        return $this;
    }

}


?>