<?php


namespace AppBundle\Utils\ListRenderer\ListModels;

use AppBundle\Entity\Attribution;
use AppBundle\Utils\ListRenderer\ActionLigne;
use AppBundle\Utils\ListRenderer\Column;
use AppBundle\Utils\ListRenderer\ListRenderer;
use Symfony\Component\Routing\Router;

class ListModelsAttributions
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

        $list->addColumn(new Column('Fonction', function (Attribution $attribution) {
            return $attribution->getFonction();
        }));
        $list->addColumn(new Column('Unité', function (Attribution $attribution) use ($router) {
            return '<a href="' . $router->generate('interne_voir_groupe', array('groupe' => $attribution->getGroupe()->getId())) . '">' . $attribution->getGroupe() . '</a>';
        }));
        $list->addColumn(new Column('Depuis le', function (Attribution $attribution) {
            return $attribution->getDateDebut();
        },
            'date(global_date_format)'));
        $list->addColumn(new Column('Jusqu\'au', function (Attribution $attribution) {
            return $attribution->GetDateFin();
        },
            'date(global_date_format)'));

        $list->addAction(new ActionLigne('Terminer', 'ban icon popupable', 'event_attribution_end'));
        $list->addAction(new ActionLigne('Supprimer', 'delete icon popupable', 'event_attribution_delete'));

        $list->setDatatable(false);
        $list->setStyle('very basic');

        return $list;
    }

}


?>