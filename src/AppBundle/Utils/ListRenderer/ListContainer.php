<?php

namespace AppBundle\Utils\ListRenderer;

use Twig_Environment;

/**
 * Cette class est un service disponible dans chaque controller.
 * Cela permet d'appeler tout les liste dàjà écrites rapidement.
 *
 * Class ListContainer
 * @package AppBundle\Utils\ListRenderer
 */
class ListContainer
{

    /** @var Twig_Environment */
    private $twig;

    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Retourne une liste vierge.
     *
     * @return ListRenderer
     */
    public function getNewListRenderer()
    {
        return new ListRenderer($this->twig);
    }

    public function getMemberListRenderer()
    {
        $list = new ListRenderer($this->twig);

        $list->setSearchBar(true);

        $list->addColumn(
            new Column(
                'Prénom',
                function ($item) {
                    return $item->getPrenom();
                }
            )
        );

        $list->addColumn(
            new Column(
                'Nom',
                function ($item) {
                    return $item->getNom();
                }
            )
        );


        $list->addColumn(
            new Column(
                'Fonction',
                function ($item) {
                    return $item->getActiveAttribution()->getFonction();
                }
            )
        );

        $list->addColumn(
            new Column(
                'Num. BS',
                function ($item) {
                    return $item->getNumeroBs();
                }
            )
        );

        $list->addColumn(
            new Column(
                'Naissance',
                function ($item) {
                    return $item->GetNaissance();
                },
                'date("d.m.Y")'
            )
        );

        $list->addAction(new ActionLigne('Afficher', 'zoom icon popupable', 'event_membre_page'));
        $list->addAction(new ActionLigne('Supprimer', 'delete icon popupable', 'event_liste_delete_element'));

        return $list;
    }

}