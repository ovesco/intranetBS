<?php


namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\AbstractList;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModel;
use AppBundle\Utils\ListUtils\ListModelInterface;
use AppBundle\Utils\ListUtils\ListRenderer;
use AppBundle\Entity\Facture;
use Symfony\Component\Routing\Router;

class ListModelsFactures extends  AbstractList
{

    public function getDefault($items,$url = null)
    {
        $twig = $this->twig;
        $router = $this->router;
        $this->setItems($items);
        $this->setUrl($url);

        $this->addColumn(new Column('Num. ref', function (Facture $facture) use ($router) {
            return 'N°' . $facture->getId();
        }));

        $this->addColumn(new Column('Statut', function (Facture $facture) { return $facture;}, 'facture_state|raw'));

        $this->addColumn(new Column('Créances', function (Facture $facture) {
            return $facture->getMontantEmisCreances();
        }, "money"));
        $this->addColumn(new Column('Rappels', function (Facture $facture) {
            return $facture->getMontantEmisRappels();
        }, "money"));
        $this->addColumn(new Column('Total', function (Facture $facture) {
            return $facture->getMontantEmis();
        }, "money"));
        $this->addColumn(new Column('Reçu', function (Facture $facture) {
            return $facture->getMontantRecu();
        }, "money"));


        $factureParameters = function (Facture $facture) {
            return array(
                'facture' => $facture->getId()
            );
        };

        $conditionRemove = function (Facture $facture) {
            return $facture->isRemovable();
        };
        $this->addActionLine(new ActionLine('Voir', 'zoom', 'app_facture_show', $factureParameters, EventPostAction::ShowModal));

        $this->addActionLine(new ActionLine('Supprimer', 'delete', 'app_facture_remove', $factureParameters, EventPostAction::RefreshList,$conditionRemove));

        $this->addActionLine(new ActionLine('Imprimer', 'print', 'app_facture_print', $factureParameters,EventPostAction::Link));


        $this->setDatatable(true);

        return $this;
    }

    /**
     * @param $items
     * @return ListRenderer
     */
    public function getSearchResults( $items, $url = null)
    {
        $twig = $this->twig;
        $router = $this->router;
        $this->setItems($items);
        $this->setUrl($url);


        $this->addColumn(new Column('Num. ref', function (Facture $facture) use ($router) {
            return 'N°' . $facture->getId();
        }));

        $this->addColumn(new Column('Débiteur', function (Facture $facture) use ($router) {

            return $facture->getDebiteur()->getOwnerAsString();

        }));

        $this->addColumn(new Column('Statut', function (Facture $facture) { return $facture;}, 'facture_state|raw'));

        $this->addColumn(new Column('Nb. Rappels', function (Facture $facture) {
            return $facture->getNombreRappels();
        }));
        $this->addColumn(new Column('Total', function (Facture $facture) {
            return $facture->getMontantEmis();
        }, "money"));
        $this->addColumn(new Column('Reçu', function (Facture $facture) {
            return $facture->getMontantRecu();
        }, "money"));


        $factureParameters = function (Facture $facture) {
            return array(
                'facture' => $facture->getId()
            );
        };

        $this->addActionLine(new ActionLine('Voir', 'zoom', 'app_facture_show', $factureParameters, EventPostAction::ShowModal));


        $this->addActionLine(new ActionLine('Supprimer', 'delete', 'app_facture_delete', $factureParameters, EventPostAction::RefreshList));

        $this->setDatatable(true);

        return $this;
    }

}


?>
