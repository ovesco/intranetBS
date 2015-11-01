<?php


namespace AppBundle\Utils\ListRenderer\ListModels;

use AppBundle\Entity\ObtentionDistinction;
use AppBundle\Utils\ListRenderer\ActionLigne;
use AppBundle\Utils\ListRenderer\Column;
use AppBundle\Utils\ListRenderer\ListRenderer;
use Symfony\Component\Routing\Router;

class ListModelsDistinctions
{

    /**
     * @param \Twig_Environment $twig
     * @param Router $router
     * @param $items
     * @return ListRenderer
     */
    static public function getDefault(\Twig_Environment $twig, Router $router, $items)
    {
        $list = new ListRenderer($twig, $items);

        $list->setSearchBar(true);

        $list->addColumn(new Column('Distinction', function (ObtentionDistinction $item) {
            return $item->getDistinction()->getNom();
        }));
        $list->addColumn(new Column('Depuis le', function (ObtentionDistinction $item) {
            return $item->getDate();
        },
            'date(global_date_format)'));

        $obtentionParameters = function (ObtentionDistinction $obtention) {
            return array(
                "obtention-distinction" => $obtention->getId()
            );
        };

        $list->addAction(new ActionLigne('Supprimer', 'delete icon popupable', 'obtention-distinction_delete', $obtentionParameters));

        $list->setDatatable(false);
        $list->setStyle('very basic');

        return $list;
    }

}


?>