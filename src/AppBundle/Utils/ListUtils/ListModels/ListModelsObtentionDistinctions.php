<?php


namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\Membre;
use AppBundle\Entity\ObtentionDistinction;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\AbstractList;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\ActionList;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModel;
use AppBundle\Utils\ListUtils\ListRenderer;
use Symfony\Component\Routing\Router;

class ListModelsObtentionDistinctions extends AbstractList
{
    public function getDefault($items, $url = null)
    {
        $twig = $this->twig;
        $router = $this->router;
        $this->setItems($items);
        $this->setUrl($url);
    }

    
    public function getMembreDistinctions($items, $url = null, Membre $membre)
    {
        $twig = $this->twig;
        $router = $this->router;
        $this->getDefault($items,$url);


        $this->addColumn(new Column('Distinction', function (ObtentionDistinction $item) {
            return $item->getDistinction()->getNom();
        }));
        $this->addColumn(new Column('Depuis le', function (ObtentionDistinction $item) {
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


        $this->addActionLine(new ActionLine('Supprimer', 'delete', 'obtention-distinction_delete', $obtentionParameters, EventPostAction::RefreshList));

        $this->addActionList(new ActionList('Ajouter', 'add', 'obtention-distinction_add_modal', $membreParameters, EventPostAction::ShowModal));

        $this->setDatatable(false);
        $this->setCssClass('very basic');

        return $this;
    }

}


?>