<?php


namespace Interne\FinancesBundle\Utils\ListModels;

use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListRenderer\Action;
use AppBundle\Utils\ListRenderer\Column;
use AppBundle\Utils\ListRenderer\ListRenderer;
use Interne\FinancesBundle\Entity\Facture;
use Symfony\Component\Routing\Router;

class ListModelsFactures
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

        $list->addColumn(new Column('Num. ref', function (Facture $facture) use ($router) {
            return '<a href="' . $router->generate('interne_finances_facture_show', array('facture' => $facture->getId())) . '">N°' . $facture->getId() . '</a>';
        }));

        $list->addColumn(new Column('Statut', function (Facture $facture) { return $facture;}, 'facture_is_payed|raw'));

        $list->addColumn(new Column('Créances', function (Facture $facture) {
            return $facture->getMontantEmisCreances();
        }, "money"));
        $list->addColumn(new Column('Rappels', function (Facture $facture) {
            return $facture->getMontantEmisRappels();
        }, "money"));
        $list->addColumn(new Column('Total', function (Facture $facture) {
            return $facture->getMontantEmis();
        }, "money"));
        $list->addColumn(new Column('Reçu', function (Facture $facture) {
            return $facture->getMontantRecu();
        }, "money"));


        $factureParameters = function (Facture $facture) {
            return array(
                'facture' => $facture->getId()
            );
        };

        $list->addAction(new Action('Supprimer', 'delete', 'interne_finances_facture_delete', $factureParameters, EventPostAction::RefreshList));

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

        $list->addColumn(new Column('Num. ref', function (Facture $facture) use ($router) {
            return '<a href="' . $router->generate('interne_finances_facture_show', array('facture' => $facture->getId())) . '">N°' . $facture->getId() . '</a>';
        }));
        $list->addColumn(new Column('Statut', function (Facture $facture) { return $facture;}, 'facture_is_payed|raw'));

        $list->addColumn(new Column('Nb. Rappels', function (Facture $facture) {
            return $facture->getNombreRappels();
        }));
        $list->addColumn(new Column('Total', function (Facture $facture) {
            return $facture->getMontantEmis();
        }, "money"));
        $list->addColumn(new Column('Reçu', function (Facture $facture) {
            return $facture->getMontantRecu();
        }, "money"));


        $factureParameters = function (Facture $facture) {
            return array(
                'facture' => $facture->getId()
            );
        };

        $list->addAction(new Action('Supprimer', 'delete', 'interne_finances_facture_delete', $factureParameters, EventPostAction::RefreshList));

        $list->setDatatable(true);

        return $list;
    }

}


?>