<?php


namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModelInterface;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\ListRenderer;
use AppBundle\Entity\Creance;
use Symfony\Component\Routing\Router;

class ListModelsCreances implements ListModelInterface
{


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


        $creanceParameters = function (Creance $creance) {
            return array(
                "creance" => $creance->getId()
            );
        };

        $list->addActionLine(new ActionLine('Afficher', 'zoom', 'app_creance_show', $creanceParameters, EventPostAction::ShowModal,null,true,false));


        //si la créance est déjà facturée, on donne la possibilité de visionner la facture.
        $factureCondition = function (Creance $creance) {
            return $creance->isFactured();
        };

        $factureParameters = function (Creance $creance) {
            return array(
                "facture" => ($creance->isFactured() ? $creance->getFacture()->getId() : null)
            );
        };


        $list->addActionLine(new ActionLine('Afficher', 'edit', 'app_facture_show', $factureParameters, EventPostAction::ShowModal,$factureCondition,true,false));



        return $list;
    }

}


?>