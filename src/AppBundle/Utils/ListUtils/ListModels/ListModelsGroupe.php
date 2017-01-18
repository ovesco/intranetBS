<?php

namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\Model;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\AbstractList;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\ActionList;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModel;
use AppBundle\Utils\ListUtils\ListModelInterface;
use AppBundle\Utils\ListUtils\ListRenderer;
use Symfony\Component\Routing\Router;
use AppBundle\Entity\Fonction;
use AppBundle\Entity\Groupe;


class ListModelsGroupe extends  AbstractList
{


    /**
     * @param $items
     * @param string $url
     * @return ListRenderer
     */
    public function getDefault($items, $url = null)
    {
        
        $this->setItems($items);
        $this->setUrl($url);
        $this->setName('groupe_default');

        //$this->setSearchBar(true);

        $this->addColumn(new Column('Nom', function (Groupe $groupe) {
            return $groupe->getNom();
        }));

        /*

        $parameters = function (Model $model) {
            return array(
                "model" => $model->getId()
            );
        };
        /* Editer le model courant *
        $edit = new ActionLine('Modifier', 'edit', 'app_model_edit', $parameters, EventPostAction::ShowModal);
        $edit->setInMass(false);
        $this->addActionLine($edit);

        $delete = new ActionLine('Supprimer', 'remove', 'app_model_remove', $parameters, EventPostAction::ShowModal);
        $delete->setInMass(false);
        $delete->setCondition(function(Model $model){return $model->isRemovable();});
        $this->addActionLine($delete);


        $this->addActionList(new ActionList('Ajouter', 'add', 'app_model_add', function(){return array();}, EventPostAction::ShowModal,null,'green'));

*/
        return $this;
    }

}


?>