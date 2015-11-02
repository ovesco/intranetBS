<?php


namespace Interne\FinancesBundle\Utils\ListModels;

use AppBundle\Utils\ListRenderer\Action;
use AppBundle\Utils\ListRenderer\Column;
use AppBundle\Utils\ListRenderer\ListRenderer;
use Interne\FinancesBundle\Entity\Facture;

class ListModelsFactures
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

        $list->addColumn(new Column('Num. ref', function (Facture $item) {
            return 'N°' . $item->GetId();
        }));
        $list->addColumn(new Column('Statut', function (Facture $item) {
            switch ($item->getStatut()) {
                case 'payee':
                    return '<i class="bordered inverted green checkmark icon popupable" data-content="Payée"></i>';
                    break;

                case 'ouverte':
                    return '<i class="bordered inverted blue ellipsis horizontal icon popupable" data-content="En attente de payement"></i>';
                    break;
            }
        }));
        $list->addColumn(new Column('Créances', function (Facture $item) {
            return $item->getMontantEmisCreances();
        }, "number_format(2, '.', ',')"));
        $list->addColumn(new Column('Rappels', function (Facture $item) {
            return $item->getMontantEmisRappels();
        }, "number_format(2, '.', ',')"));
        $list->addColumn(new Column('Total', function (Facture $item) {
            return $item->getMontantEmis();
        }, "number_format(2, '.', ',')"));
        $list->addColumn(new Column('Reçu', function (Facture $item) {
            return $item->getMontantRecu();
        }, "number_format(2, '.', ',')"));

        $list->addAction(new Action('Afficher', 'zoom icon popupable', 'event_creance_show'));
        $list->addAction(new Action('Supprimer', 'delete icon popupable', 'event_creance_delete'));

        $list->setDatatable(false);

        return $list;
    }

}


?>