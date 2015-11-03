<?php

namespace AppBundle\Utils\ListRenderer\ListModels;

use AppBundle\Entity\Membre;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListRenderer\Action;
use AppBundle\Utils\ListRenderer\Column;
use AppBundle\Utils\ListRenderer\ListRenderer;
use Symfony\Component\Routing\Router;

class ListModelsMembre
{

    static public function getEffectifs(\Twig_Environment $twig, Router $router, $items)
    {
        $list = ListModelsMembre::getDefault($twig, $router, $items);

        $list->setName('effectifs');

        $attributionParameters = function (Membre $membre) {
            // TODO: ne marche pas s'il y a plusieurs attributions
            return array(
                "attribution" => $membre->getActiveAttribution()->getId()
            );
        };

        /* Supprimer l'attribution courante */
        $list->addAction(new Action('Supprimer', 'delete', 'attribution_delete', $attributionParameters, EventPostAction::RefreshList));

        return $list;
    }

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

        $list->addColumn(new Column('Prénom', function (Membre $membre) use ($router) {
            return '<a href="' . $router->generate('interne_voir_membre', array('membre' => $membre->getId())) . '">' . $membre->getPrenom() . '</a>';
        }));
        $list->addColumn(new Column('Nom', function (Membre $membre) use ($router) {
            return '<a href="' . $router->generate('interne_voir_famille', array('famille' => $membre->getFamille()->getId())) . '">' . $membre->getNom() . '</a>';
        }));
        $list->addColumn(new Column('Fonction', function (Membre $membre) use ($router) {
            return $membre->getActiveAttribution();
        }));
        $list->addColumn(new Column('Naissance', function (Membre $membre) {
            return $membre->GetNaissance();
        },
            'date(global_date_format)'));

        return $list;
    }

    static public function getFraterie(\Twig_Environment $twig, Router $router, $items)
    {
        $list = new ListRenderer($twig, $items);

        $list->addColumn(new Column('Prénom', function (Membre $membre) use ($router) {
            return '<a href="' . $router->generate('interne_voir_membre', array('membre' => $membre->getId())) . '">' . $membre->getPrenom() . '</a>';
        }));
        $list->addColumn(new Column('Fonction', function (Membre $membre) {
            return $membre->getActiveAttribution()->getFonction();
        }));
        $list->addColumn(new Column('Naissance', function (Membre $membre) {
            return $membre->GetNaissance();
        },
            'date(global_date_format)'));

        $list->setDatatable(false);
        $list->setStyle('very basic');

        return $list;
    }
}


?>