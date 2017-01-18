<?php


namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\Attribution;
use AppBundle\Entity\Membre;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\AbstractList;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\ActionList;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModel;
use AppBundle\Utils\ListUtils\ListRenderer;
use Symfony\Component\Routing\Router;

class ListModelsAttributions extends AbstractList
{

    public function getDefault( $items, $url = null)
    {
        $this->setItems($items);
        $this->setUrl($url);
    }
    
    /**
     * @param $items
     * @param Membre $membre
     * @param string $url
     * @return ListRenderer
     */
    public function getMembreAttribution( $items, $url , Membre $membre)
    {
        $twig = $this->twig;
        $router = $this->router;
        $this->getDefault($items,$url);
        $this->setUrl($url);

        $this->setName('attributions');

        $this->addColumn(new Column('Fonction', function (Attribution $attribution) {
            return $attribution->getFonction();
        }));
        $this->addColumn(new Column('Unité', function (Attribution $attribution) use ($router) {
            return '<a href="' . $router->generate('app_groupe_show', array('groupe' => $attribution->getGroupe()->getId())) . '">' . $attribution->getGroupe() . '</a>';
        }));
        $this->addColumn(new Column('Depuis le', function (Attribution $attribution) {
            return $attribution->getDateDebut();
        },
            'date(global_date_format)'));
        $this->addColumn(new Column('Jusqu\'au', function (Attribution $attribution) {
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

        $this->addActionLine(new ActionLine('Modifier', 'edit', 'app_attribution_edit', $attributionParameters, EventPostAction::ShowModal));
        $this->addActionLine(new ActionLine('Terminer', 'ban', 'app_attribution_edit', $attributionParameters, EventPostAction::ShowModal));
        $this->addActionLine(new ActionLine('Supprimer', 'delete', 'app_attribution_delete', $attributionParameters, EventPostAction::RefreshList));

        $this->addActionList(new ActionList('Ajouter', 'add', 'app_attribution_add_tomembre', $membreParameters, EventPostAction::ShowModal));

        $this->setDatatable(false);
        $this->setCssClass('very basic');

        return $this;
    }

}


?>