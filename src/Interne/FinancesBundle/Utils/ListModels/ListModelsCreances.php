<?php


namespace Interne\FinancesBundle\Utils\ListModels;

use AppBundle\Utils\ListRenderer\Action;
use AppBundle\Utils\ListRenderer\Column;
use AppBundle\Utils\ListRenderer\ListRenderer;
use Interne\FinancesBundle\Entity\Creance;

class ListModelsCreances
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

        /*

        $list->addColumn(new Column('Facture', function (Creance $item) {
            if ($item->getFacture() != null)
                return 'N°' . $item->getFacture()->GetId();
            else
                return '<i class="bordered inverted orange wait icon popupable" data-content="En attente"></i>';
        }));
        */

        $list->addColumn(new Column('Motif', function (Creance $item) {
            return $item->getTitre();
        }));

        /*
        $list->addColumn(new Column('Montant', function (Creance $item) {
            return $item->getMontantEmis();
        }, "number_format(2, '.', ',')"));
        $list->addColumn(new Column('Montant perçu', function (Creance $item) {
            return $item->getMontantRecu();
        }, "number_format(2, '.', ',')"));

        $list->addAction(new Action('Afficher', 'zoom icon popupable', 'event_creance_show'));
        $list->addAction(new Action('Supprimer', 'delete icon popupable', 'event_creance_delete'));

        */
        //$list->setDatatable(true);

        return $list;
    }

}


?>