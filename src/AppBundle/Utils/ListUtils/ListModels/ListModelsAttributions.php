<?php


namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\Attribution;
use AppBundle\Entity\Membre;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\ActionList;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModel;
use AppBundle\Utils\ListUtils\ListRenderer;
use Symfony\Component\Routing\Router;

class ListModelsAttributions extends ListModel
{

    /**
     * @param $items
     * @param Membre $membre
     * @param string $url
     * @return ListRenderer
     */
    public function getDefault( $items, Membre $membre, $url = null)
    {
        $twig = $this->twig;
        $router = $this->router;
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

        $membreParameters = function () use ($membre) {
            return array(
                "membre" => $membre->getId()
            );
        };

        $list->addActionLine(new ActionLine('Modifier', 'edit', 'app_attribution_edit', $attributionParameters, EventPostAction::ShowModal));
        $list->addActionLine(new ActionLine('Terminer', 'ban', 'app_attribution_edit', $attributionParameters, EventPostAction::ShowModal));
        $list->addActionLine(new ActionLine('Supprimer', 'delete', 'app_attribution_delete', $attributionParameters, EventPostAction::RefreshList));

        $list->addActionList(new ActionList('Ajouter', 'add', 'app_attribution_add_tomembre', $membreParameters, EventPostAction::ShowModal));

        $list->setDatatable(false);
        $list->setStyle('very basic');

        return $list;
    }

}


?>