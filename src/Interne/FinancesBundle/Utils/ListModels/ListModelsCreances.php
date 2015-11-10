<?php


namespace Interne\FinancesBundle\Utils\ListModels;

use AppBundle\Utils\ListUtils\Action;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModelInterface;
use AppBundle\Utils\ListUtils\ListRenderer;
use Interne\FinancesBundle\Entity\Creance;
use Symfony\Component\Routing\Router;

class ListModelsCreances implements ListModelInterface
{

    static public function getRepresentedClass(){
        return 'Interne\FinancesBundle\Entity\Creance';
    }

    /**
     * @param \Twig_Environment $twig
     * @param $items
     * @param Router $router
     * @param string $url
     * @return ListRenderer
     */
    static public function getDefault(\Twig_Environment $twig, Router $router, $items, $url = null)
    {
        $list = new ListRenderer($twig, $items);
        $list->setUrl($url);
        $list->setSearchBar(true);

        $list->addColumn(new Column('Facture', function (Creance $item) { return $item; },'creance_facture_status|raw'));
        $list->addColumn(new Column('Etat', function (Creance $item) { return $item; },'creance_is_payed|raw'));

        $list->addColumn(new Column('Motif', function (Creance $item) {
            return $item->getTitre();
        }));


        $list->addColumn(new Column('Montant', function (Creance $item) {
            return $item->getMontantEmis();
        }, "money"));
        $list->addColumn(new Column('Montant perçu', function (Creance $item) {
            return $item->getMontantRecu();
        }, "money"));


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
    static public function getSearchResults(\Twig_Environment $twig, Router $router, $items, $url = null)
    {
        $list = new ListRenderer($twig, $items);
        $list->setUrl($url);
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
        }, "money"));
        $list->addColumn(new Column('Montant perçu', function (Creance $item) {
            return $item->getMontantRecu();
        }, "money"));


        //$list->addAction(new Action('Afficher', 'zoom icon popupable', 'event_creance_show'));
        //$list->addAction(new Action('Supprimer', 'delete icon popupable', 'event_creance_delete'));


        //$list->setDatatable(true);

        return $list;
    }

}


?>