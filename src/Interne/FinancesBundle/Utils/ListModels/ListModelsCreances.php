<?php


namespace Interne\FinancesBundle\Utils\ListModels;

use AppBundle\Utils\ListRenderer\Action;
use AppBundle\Utils\ListRenderer\Column;
use AppBundle\Utils\ListRenderer\ListRenderer;
use Interne\FinancesBundle\Entity\Creance;
use Symfony\Component\Routing\Router;

class ListModelsCreances
{

    /**
     * @param \Twig_Environment $twig
     * @param $items
     * @return ListRenderer
     */
    static public function getDefault(\Twig_Environment $twig, Router $router, $items)
    {
        $list = new ListRenderer($twig, $items);

        $list->setSearchBar(true);

        $list->addColumn(new Column('Facture', function (Creance $item) { return $item; },'creance_facture_status|raw'));
        $list->addColumn(new Column('Etat', function (Creance $item) { return $item; },'creance_is_payed|raw'));

        $list->addColumn(new Column('Motif', function (Creance $item) {
            return $item->getTitre();
        }));

        $list->addColumn(new Column('Montant', function (Creance $item) {
            return $item->getMontantEmis();
        }, "number_format(2, '.', ',')"));
        $list->addColumn(new Column('Montant perçu', function (Creance $item) {
            return $item->getMontantRecu();
        }, "number_format(2, '.', ',')"));

        //$list->addAction(new Action('Afficher', 'zoom icon popupable', 'event_creance_show'));
        //$list->addAction(new Action('Supprimer', 'delete icon popupable', 'event_creance_delete'));


        //$list->setDatatable(true);

        return $list;
    }

    /**
     * @param \Twig_Environment $twig
     * @param $items
     * @return ListRenderer
     */
    static public function getSearchResults(\Twig_Environment $twig, Router $router, $items)
    {
        $list = new ListRenderer($twig, $items);

        $list->setSearchBar(true);

        $list->addColumn(new Column('Facture', function (Creance $item) { return $item; },'creance_facture_status|raw'));
        $list->addColumn(new Column('Etat', function (Creance $item) { return $item; },'creance_is_payed|raw'));

        $list->addColumn(new Column('Motif', function (Creance $item) {
            return $item->getTitre();
        }));

        $list->addColumn(new Column('Debiteur', function (Creance $item) {
            return $item->getDebiteur()->getOwnerAsString();
        }));

        $list->addColumn(new Column('Montant', function (Creance $item) {
            return $item->getMontantEmis();
        }, "number_format(2, '.', ',')"));
        $list->addColumn(new Column('Montant perçu', function (Creance $item) {
            return $item->getMontantRecu();
        }, "number_format(2, '.', ',')"));

        //$list->addAction(new Action('Afficher', 'zoom icon popupable', 'event_creance_show'));
        //$list->addAction(new Action('Supprimer', 'delete icon popupable', 'event_creance_delete'));


        //$list->setDatatable(true);

        return $list;
    }

}


?>