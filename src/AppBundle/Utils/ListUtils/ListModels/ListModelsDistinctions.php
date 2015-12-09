<?php


namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\Membre;
use AppBundle\Entity\ObtentionDistinction;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\ActionList;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListRenderer;
use Symfony\Component\Routing\Router;

class ListModelsDistinctions
{


    /**
     * @param \Twig_Environment $twig
     * @param Router $router
     * @param $items
     * @param Membre $membre
     * @param string $url
     * @return ListRenderer
     */
    static public function getDefault(\Twig_Environment $twig, Router $router, $items, Membre $membre, $url = null)
    {
        $list = new ListRenderer($twig, $items);
        $list->setUrl($url);

        $list->setSearchBar(true);

        $list->addColumn(new Column('Distinction', function (ObtentionDistinction $item) {
            return $item->getDistinction()->getNom();
        }));
        $list->addColumn(new Column('Depuis le', function (ObtentionDistinction $item) {
            return $item->getDate();
        },
            'date(global_date_format)'));

        $obtentionParameters = function (ObtentionDistinction $obtention) {
            return array(
                "obtention-distinction" => $obtention->getId()
            );
        };

        $membreParameters = function () use ($membre) {
            return array(
                "membre" => $membre->getId()
            );
        };


        $list->addActionLine(new ActionLine('Supprimer', 'delete', 'obtention-distinction_delete', $obtentionParameters, EventPostAction::RefreshList));

        $list->addActionList(new ActionList('Ajouter', 'add', 'app_obtention_modaladd', $membreParameters, EventPostAction::ShowModal));

        $list->setDatatable(false);
        $list->setStyle('very basic');

        return $list;
    }

}


?>