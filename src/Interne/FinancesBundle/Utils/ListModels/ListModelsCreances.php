<?php


namespace Interne\FinancesBundle\Utils\ListModels;

use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListRenderer\Action;
use AppBundle\Utils\ListRenderer\Column;
use AppBundle\Utils\ListRenderer\ListRenderer;
use Interne\FinancesBundle\Entity\Creance;
use Symfony\Component\Routing\Router;

class ListModelsCreances
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

        $list->addColumn(new Column('Facture', function (Creance $item) {
            if ($item->getFacture() != null)
                return 'N°' . $item->getFacture()->GetId();
            else
                return '<i class="bordered inverted orange wait icon popupable" data-content="En attente"></i>';
        }));

        $list->addColumn(new Column('Motif', function (Creance $item) {
            return $item->getTitre();
        }));
        $list->addColumn(new Column('Montant', function (Creance $item) {
            return $item->getMontantEmis();
        }, "number_format(2, '.', ',')"));
        $list->addColumn(new Column('Montant perçu', function (Creance $item) {
            return $item->getMontantRecu();
        }, "number_format(2, '.', ',')"));


        $creanceParameters = function (Creance $creance) {
            return array(
                'creance' => $creance->getId()
            );
        };

        $list->addAction(new Action('Supprimer', 'delete', 'interne_finances_creance_delete', $creanceParameters, EventPostAction::RefreshList));


        $list->setDatatable(true);

        return $list;
    }

    /**
     * @param \Twig_Environment $twig
     * @param Router $router
     * @param $items
     * @return ListRenderer
     */
    static public function getSearchResults(\Twig_Environment $twig, Router $router, $items)
    {
        $list = new ListRenderer($twig, $items);

        $list->setSearchBar(true);

        $list->addColumn(new Column('Facture', function (Creance $item) {
            if ($item->getFacture() != null)
                return 'N°' . $item->getFacture()->GetId();
            else
                return '<i class="bordered inverted orange wait icon popupable" data-content="En attente"></i>';
        }));

        $list->addColumn(new Column('Motif', function (Creance $creance) use ($router) {
            return '<a href="' . $router->generate('interne_finances_creance_show', array('creance' => $creance->getId())) . '">' . $creance->getTitre() . '</a>';
        }));

        $list->addColumn(new Column('Montant', function (Creance $creance) {
            return $creance->getMontantEmis();
        }, "number_format(2, '.', ',')"));

        $list->addColumn(new Column('Montant perçu', function (Creance $creance) {
            return $creance->getMontantRecu();
        }, "number_format(2, '.', ',')"));


        $creanceParameters = function (Creance $creance) {
            return array(
                'creance' => $creance->getId()
            );
        };

        $list->addAction(new Action('Supprimer', 'delete', 'interne_finances_creance_delete', $creanceParameters, EventPostAction::RefreshList));

        $list->setDatatable(true);

        return $list;
    }

}


?>