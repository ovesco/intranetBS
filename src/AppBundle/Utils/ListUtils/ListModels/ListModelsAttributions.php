<?php


namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\Attribution;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\Action;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModelInterface;
use AppBundle\Utils\ListUtils\ListRenderer;
use Symfony\Component\Routing\Router;

class ListModelsAttributions implements ListModelInterface
{

    static public function getRepresentedClass(){
        return 'AppBundle\Entity\Attribution';
    }

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

        $list->setName('attributions');

        $list->setSearchBar(true);

        $list->addColumn(new Column('Fonction', function (Attribution $attribution) {
            return $attribution->getFonction();
        }));
        $list->addColumn(new Column('Unité', function (Attribution $attribution) use ($router) {
            return '<a href="' . $router->generate('app_groupe_show', array('groupe' => $attribution->getGroupe()->getId())) . '">' . $attribution->getGroupe() . '</a>';
        }));
        $list->addColumn(new Column('Depuis le', function (Attribution $attribution) {
            return $attribution->getDateDebut();
        },
            'date(global_date_format)'));
        $list->addColumn(new Column('Jusqu\'au', function (Attribution $attribution) {
            return $attribution->GetDateFin();
        },
            'date(global_date_format)'));

        $attributionParameters = function (Attribution $attribution) {
            return array(
                "attribution" => $attribution->getId()
            );
        };

        $list->addAction(new Action('Modifier', 'edit', 'attribution_edit_modal', $attributionParameters, EventPostAction::ShowModal));
        $list->addAction(new Action('Terminer', 'ban', 'attribution_edit_modal', $attributionParameters, EventPostAction::ShowModal));
        $list->addAction(new Action('Supprimer', 'delete', 'attribution_delete', $attributionParameters, EventPostAction::RefreshList));

        $list->setDatatable(false);
        $list->setStyle('very basic');

        return $list;
    }

}


?>