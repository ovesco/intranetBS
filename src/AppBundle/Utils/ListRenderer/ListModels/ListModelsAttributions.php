<?php


namespace AppBundle\Utils\ListRenderer\ListModels;

use AppBundle\Entity\Attribution;
use AppBundle\Utils\ListRenderer\ActionLigne;
use AppBundle\Utils\ListRenderer\Column;
use AppBundle\Utils\ListRenderer\ListRenderer;

class ListModelsAttributions
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

        $list->addColumn(new Column('Fonction', function (Attribution $item) {
            return $item->getFonction();
        }));
        $list->addColumn(new Column('Unité', function (Attribution $item) {
            return $item->getGroupe();
        }));
        $list->addColumn(new Column('Depuis le', function (Attribution $item) {
            return $item->getDateDebut();
        },
            'date(global_date_format)'));
        $list->addColumn(new Column('Jusqu\'au', function (Attribution $item) {
            return $item->GetDateFin();
        },
            'date(global_date_format)'));

        $list->addAction(new ActionLigne('Terminer', 'zoom icon popupable', 'event_attribution_end'));
        $list->addAction(new ActionLigne('Supprimer', 'delete icon popupable', 'event_attribution_delete'));

        $list->setDatatable(false);
        $list->setStyle('very basic');

        return $list;
    }

}


?>