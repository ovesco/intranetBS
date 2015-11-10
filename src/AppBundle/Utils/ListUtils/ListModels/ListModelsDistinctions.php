<?php


namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\ObtentionDistinction;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\Action;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModelInterface;
use AppBundle\Utils\ListUtils\ListRenderer;
use Symfony\Component\Routing\Router;

class ListModelsDistinctions implements ListModelInterface
{

    static public function getRepresentedClass(){
        return 'AppBundle\Entity\ObtentionDistinction';
    }

    /**
     * @param \Twig_Environment $twig
     * @param Router $router
     * @param $items
     * @param string $url
     * @return ListRenderer
     */
    static public function getDefault(\Twig_Environment $twig, Router $router, $items, $url = null)
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

        $list->addAction(new Action('Supprimer', 'delete', 'obtention-distinction_delete', $obtentionParameters, EventPostAction::RefreshList));

        $list->setDatatable(false);
        $list->setStyle('very basic');

        return $list;
    }

}


?>