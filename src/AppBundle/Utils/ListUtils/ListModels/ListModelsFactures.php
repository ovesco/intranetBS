<?php


namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModelInterface;
use AppBundle\Utils\ListUtils\ListRenderer;
use AppBundle\Entity\Facture;
use Symfony\Component\Routing\Router;

class ListModelsFactures implements ListModelInterface
{


    /**
     * @param \Twig_Environment $twig
     * @param Router $router
     * @param $items
     * @param string $url
     * @return ListRenderer
     */
    static public function getDefault(\Twig_Environment $twig, Router $router, $items,$url = null)
    {
        $list = new ListRenderer($twig, $items);
        $list->setUrl($url);
        $list->setSearchBar(true);

        $list->addColumn(new Column('Num. ref', function (Facture $facture) use ($router) {
            return 'N°' . $facture->getId();
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

        $list->addActionLine(new ActionLine('Voir', 'zoom', 'app_facture_show', $factureParameters, EventPostAction::ShowModal));

        $list->addActionLine(new ActionLine('Supprimer', 'delete', 'interne_finances_facture_delete', $factureParameters, EventPostAction::RefreshList));

        $list->setDatatable(true);

        return $list;
    }

    /**
     * @param \Twig_Environment $twig
     * @param Router $router
     * @param $items
     * @return ListRenderer
     */
    static public function getSearchResults(\Twig_Environment $twig, Router $router, $items, $url = null)
    {
        $list = new ListRenderer($twig, $items);
        $list->setUrl($url);

        $list->setSearchBar(true);

        $list->addColumn(new Column('Num. ref', function (Facture $facture) use ($router) {
            return 'N°' . $facture->getId();
        }));

        $list->addColumn(new Column('Débiteur', function (Facture $facture) use ($router) {

            return $facture->getDebiteur()->getOwnerAsString();

        }));

        $list->addColumn(new Column('Statut', function (Facture $facture) { return $facture;}, 'facture_state|raw'));

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

        $list->addActionLine(new ActionLine('Voir', 'zoom', 'app_facture_show', $factureParameters, EventPostAction::ShowModal));


        $list->addActionLine(new ActionLine('Supprimer', 'delete', 'app_facture_delete', $factureParameters, EventPostAction::RefreshList));

        $list->setDatatable(true);

        return $list;
    }

}


?>