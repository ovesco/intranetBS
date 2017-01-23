<?php


namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\Distinction;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\ActionList;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModel;
use AppBundle\Utils\ListUtils\ListRenderer;
use Symfony\Component\Routing\Router;

class ListModelsDistinctions extends ListModel
{
    /**
     * @param $items
     * @param null $url
     * @return ListRenderer
     */
    public function getGestion($items, $url = null)
    {
        $twig = $this->twig;
        $router = $this->router;
        $list = new ListRenderer($twig, $items);
        $list->setUrl($url);

        $list->addColumn(new Column('Distinction', function (Distinction $item) {
            return $item->getNom();
        }));

        $list->addColumn(new Column('Remarque', function (Distinction $item) {
            return $item->getRemarques();
        }));

        $parameters = function (Distinction $distinction) {
            return array(
                "distinction" => $distinction->getId()
            );
        };

        /* Editer la distinction courante */
        $edit = new ActionLine('Modifier', 'edit', 'app_distinction_edit', $parameters, EventPostAction::ShowModal);
        $edit->setInMass(false);
        $list->addActionLine($edit);

        /* Supprimer la distinction courante */
        $delete = new ActionLine('Supprimer', 'remove', 'app_distinction_remove', $parameters, EventPostAction::RefreshPage);
        $delete->setInMass(false);
        $delete->setCondition(function(Distinction $distinction){return $distinction->isRemovable();});
        $list->addActionLine($delete);

        /* ajouter une distinction */
        $list->addActionList(new ActionList('Ajouter', 'add', 'app_distinction_add', function(){return array();}, EventPostAction::ShowModal,null,'green'));



        return $list;
    }

}


?>