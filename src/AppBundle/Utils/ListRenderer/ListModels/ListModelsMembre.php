<?php

namespace AppBundle\Utils\ListRenderer\ListModels;

use AppBundle\Entity\Membre;
use AppBundle\Utils\ListRenderer\ActionLigne;
use AppBundle\Utils\ListRenderer\Column;
use AppBundle\Utils\ListRenderer\ListRenderer;

class ListModelsMembre
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

        $list->addColumn(new Column('Prénom', function (Membre $item) {
            return $item->getPrenom();
        }));
        $list->addColumn(new Column('Nom', function (Membre $item) {
            return $item->getNom();
        }));
        $list->addColumn(new Column('Fonction', function (Membre $item) {
            return $item->getActiveAttribution()->getFonction();
        }));
        $list->addColumn(new Column('Naissance', function (Membre $item) {
            return $item->GetNaissance();
        },
            'date(global_date_format)'));

        $list->addAction(new ActionLigne('Afficher', 'zoom icon popupable', 'event_membre_show_page'));
        $list->addAction(new ActionLigne('Supprimer', 'delete icon popupable', 'event_liste_delete_element'));

        return $list;
    }


    static public function getFraterie(\Twig_Environment $twig, $items)
    {
        $list = new ListRenderer($twig, $items);

        $list->addColumn(new Column('Prénom', function (Membre $item) {
            return $item->getPrenom();
        }));
        $list->addColumn(new Column('Fonction', function (Membre $item) {
            return $item->getActiveAttribution()->getFonction();
        }));
        $list->addColumn(new Column('Naissance', function (Membre $item) {
            return $item->GetNaissance();
        },
            'date(global_date_format)'));

        $list->addAction(new ActionLigne('Afficher', 'zoom icon popupable', 'event_membre_show_page'));

        $list->setDatatable(false);
        $list->setStyle('very basic');

        return $list;
    }
}


?>