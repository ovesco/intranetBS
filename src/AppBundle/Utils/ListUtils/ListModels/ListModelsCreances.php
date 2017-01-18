<?php


namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Utils\ListUtils\AbstractList;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\ListRenderer;
use AppBundle\Entity\Creance;
use Symfony\Component\Routing\Router;
use AppBundle\Utils\ListUtils\ActionList;
use AppBundle\Entity\Debiteur;

class ListModelsCreances extends  AbstractList
{


    /**
     * @param $items
     * @param string $url
     * @return ListRenderer
     */
    public function getDefault($items, $url = null)
    {
        $twig = $this->twig;
        $router = $this->router;
        $this->setItems($items);
        $this->setUrl($url);

        $this->addColumn(new Column('Facture', function (Creance $item) { return $item->getFacture(); },'ref|raw'));
        $this->addColumn(new Column('Etat', function (Creance $item) { return $item; },'creance_state|raw'));

        $this->addColumn(new Column('Motif', function (Creance $item) {
            return $item->getTitre();
        }));


        $this->addColumn(new Column('Montant', function (Creance $item) {
            return $item->getMontantEmis();
        }, "money"));
        /*
        $this->addColumn(new Column('Montant perçu', function (Creance $item) {
            return $item->getMontantRecu();
        }, "money"));

        */

        $parameters = function (Creance $item) {
            return array(
                'creance' => $item->getId()
            );
        };

        $removeCondition = function (Creance $creance) {
            return !$creance->isFactured();
        };

        $this->addActionLine(new ActionLine('Voir', 'zoom', 'app_creance_show', $parameters, EventPostAction::ShowModal));

        /* todo CMR de NUR comment je fait pour que cette action soit uniquement dans les lignes et pas dans le bouton de mass? */
        $this->addActionLine(new ActionLine('Supprimer', 'remove', 'app_creance_remove', $parameters, EventPostAction::RefreshPage,$removeCondition));



        return $this;
    }


    /**
     * @param $items
     * @param null $url
     * @param Debiteur $debiteur
     * @return ListRenderer
     */
    public function getForDebiteur($items, $url = null,Debiteur $debiteur)
    {
        $twig = $this->twig;
        $router = $this->router;
        $this->getDefault($items, $url);

        $this->addActionList(new ActionList('Ajouter', 'add', 'app_creance_create',array('debiteur' => $debiteur->getId()), EventPostAction::ShowModal,null,'green'));

        $this->addActionList(new ActionList('Facturer', 'file text', 'app_debiteur_facturation',array('debiteur' => $debiteur->getId()), EventPostAction::RefreshList,null,'blue'));

        return $this;
    }

    /**
     * @param $items
     * @return ListRenderer
     */
    public function getSearchResults( $items, $url = null, $thisSessionKey = null)
    {
        $twig = $this->twig;
        $router = $this->router;
        $this->setItems($items);
        $this->setUrl($url);


        $this->addColumn(new Column('Facture', function (Creance $item) { return $item->getFacture(); },'ref|raw'));
        $this->addColumn(new Column('Etat', function (Creance $item) { return $item; },'creance_state|raw'));

        $this->addColumn(new Column('Motif', function (Creance $item) {
            return $item->getTitre();
        }));

        $this->addColumn(new Column('Debiteur', function (Creance $item) {
            return $item->getDebiteur()->getOwnerAsString();
        }));


        $this->addColumn(new Column('Montant', function (Creance $item) {
            return $item->getMontantEmis();
        }, "money"));
        $this->addColumn(new Column('Montant perçu', function (Creance $item) {
            return $item->getMontantRecu();
        }, "money"));


        $creanceParameters = function (Creance $creance) {
            return array(
                "creance" => $creance->getId()
            );
        };

        $this->addActionLine(new ActionLine('Afficher', 'zoom', 'app_creance_show', $creanceParameters, EventPostAction::ShowModal,null,true,false));


        //si la créance est déjà facturée, on donne la possibilité de visionner la facture.
        $factureCondition = function (Creance $creance) {
            return $creance->isFactured();
        };

        $factureParameters = function (Creance $creance) {
            return array(
                "facture" => ($creance->isFactured() ? $creance->getFacture()->getId() : null)
            );
        };


        $this->addActionLine(new ActionLine('Afficher', 'edit', 'app_facture_show', $factureParameters, EventPostAction::ShowModal,$factureCondition,true,false));


        $this->addActionList(new ActionList('Facturer les créance ouvertes', 'file text', 'app_creance_facturation',array('list_session_key' => $thisSessionKey), EventPostAction::RefreshPage,null,'blue'));

        return $this;
    }

}


?>