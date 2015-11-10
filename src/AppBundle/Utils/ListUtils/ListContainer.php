<?php

namespace AppBundle\Utils\ListUtils;

use AppBundle\Utils\ListUtils\ListModels\ListModelsAttributions;
use AppBundle\Utils\ListUtils\ListModels\ListModelsDistinctions;
use AppBundle\Utils\ListUtils\ListModels\ListModelsMembre;
use Interne\FinancesBundle\Utils\ListModels\ListModelsCreances;
use Interne\FinancesBundle\Utils\ListModels\ListModelsFactures;
use Symfony\Component\Routing\Router;
use Twig_Environment;


/**
 * Cette class est un service disponible dans chaque controller.
 * Cela permet d'appeler tout les liste déjà écrites rapidement.
 *
 * Class ListContainer
 * @package AppBundle\Utils\ListUtils
 */
class ListContainer
{

    /** @var Twig_Environment */
    private $twig;

    private $router;

    public function __construct(\Twig_Environment $twig, Router $router)
    {
        $this->twig = $twig;
        $this->router = $router;
    }

    /**
     * Retourne une liste vierge.
     *
     * @return ListRenderer
     */
    public function getNewListRenderer()
    {
        return new ListRenderer($this->twig, $this->router);
    }


    /*
     * Please ONLY snake case because this used in routing as model parameter
     */
    const Membre = 'membre';
    const MembreEffectifs = 'membre_effectifs';
    const MembreFraterie = 'membre_fraterie';
    const Attribution = 'attribution';
    const Distinction = 'aistinction';
    const CreanceSearchResults = 'creance_search_results';
    const Creance = 'creance';
    const Facture = 'facture';
    const FactureSearchResults = 'facture_search_results';

    public function getModel($model, $items, $url = null)
    {
        switch ($model) {
            case ListContainer::Membre:
                return ListModelsMembre::getDefault($this->twig, $this->router, $items, $url);

            case ListContainer::MembreEffectifs:
                return ListModelsMembre::getEffectifs($this->twig, $this->router, $items, $url);

            case ListContainer::MembreFraterie:
                return ListModelsMembre::getFraterie($this->twig, $this->router, $items, $url);

            case ListContainer::Attribution:
                return ListModelsAttributions::getDefault($this->twig, $this->router, $items, $url);

            case ListContainer::Distinction:
                return ListModelsDistinctions::getDefault($this->twig, $this->router, $items, $url);

            case ListContainer::Creance:
                return ListModelsCreances::getDefault($this->twig, $this->router, $items, $url);

            case ListContainer::CreanceSearchResults:
                return ListModelsCreances::getSearchResults($this->twig, $this->router, $items, $url);

            case ListContainer::Facture:
                return ListModelsFactures::getDefault($this->twig, $this->router, $items, $url);

            case ListContainer::FactureSearchResults:
                return ListModelsFactures::getSearchResults($this->twig, $this->router, $items, $url);
        }
    }

    public function getRepresentedClass($model)
    {
        switch ($model) {
            case ListContainer::Membre:
                return ListModelsMembre::getRepresentedClass();
            case ListContainer::MembreEffectifs:
                return ListModelsMembre::getRepresentedClass();

            case ListContainer::MembreFraterie:
                return ListModelsMembre::getRepresentedClass();

            case ListContainer::Attribution:
                return ListModelsAttributions::getRepresentedClass();

            case ListContainer::Distinction:
                return ListModelsDistinctions::getRepresentedClass();

            case ListContainer::Creance:
                return ListModelsCreances::getRepresentedClass();

            case ListContainer::CreanceSearchResults:
                return ListModelsCreances::getRepresentedClass();

            case ListContainer::Facture:
                return ListModelsFactures::getRepresentedClass();

            case ListContainer::FactureSearchResults:
                return ListModelsFactures::getRepresentedClass();
        }
    }
}