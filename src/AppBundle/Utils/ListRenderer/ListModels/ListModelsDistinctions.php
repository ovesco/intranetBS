<?php


namespace AppBundle\Utils\ListRenderer\ListModels;

use AppBundle\Entity\Distinction;
use AppBundle\Utils\ListRenderer\ActionLigne;
use AppBundle\Utils\ListRenderer\Column;
use AppBundle\Utils\ListRenderer\ListRenderer;

class ListModelsDistinctions
{

    /**
     * @param \Twig_Environment $twig
     * @param $items
     * @return ListRenderer
     */
    static public function getDefault(\Twig_Environment $twig, $items)
    {
        $list = new ListRenderer($twig, $items);

        $list->setSearchBar(true);

        $list->addColumn(new Column('Distinction', function (Distinction $item) {
            return $item->getNom();
        }));
        $list->addColumn(new Column('Depuis le', function (Distinction $item) {
            return $item->getDate();
        },
            'date(global_date_format)'));

        $list->addAction(new ActionLigne('Supprimer', 'delete icon popupable', 'event_attribution_delete'));

        $list->setDatatable(false);
        $list->setStyle('very basic');

        return $list;
    }

}


?>