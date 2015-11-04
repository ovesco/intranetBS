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
        $list->addColumn(new Column('Statut', function (Facture $facture) {
            switch ($facture->getStatut()) {
                case 'payee':
                    return '<i class="bordered inverted green checkmark icon popupable" data-content="Payée"></i>';
                    break;

                case 'ouverte':
                    return '<i class="bordered inverted blue ellipsis horizontal icon popupable" data-content="En attente de payement"></i>';
                    break;
            }
        }));
        $list->addColumn(new Column('Créances', function (Facture $facture) {
            return $facture->getMontantEmisCreances();
        }, "number_format(2, '.', ',')"));
        $list->addColumn(new Column('Rappels', function (Facture $facture) {
            return $facture->getMontantEmisRappels();
        }, "number_format(2, '.', ',')"));
        $list->addColumn(new Column('Total', function (Facture $facture) {
            return $facture->getMontantEmis();
        }, "number_format(2, '.', ',')"));
        $list->addColumn(new Column('Reçu', function (Facture $facture) {
            return $facture->getMontantRecu();
        }, "number_format(2, '.', ',')"));


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