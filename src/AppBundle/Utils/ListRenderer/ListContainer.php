<?php

namespace AppBundle\Utils\ListRenderer;

use AppBundle\Utils\ListRenderer\ListModels\ListModelsAttributions;
use AppBundle\Utils\ListRenderer\ListModels\ListModelsDistinctions;
use AppBundle\Utils\ListRenderer\ListModels\ListModelsMembre;
use Interne\FinancesBundle\Utils\ListModels\ListModelsCreances;
use Interne\FinancesBundle\Utils\ListModels\ListModelsFactures;
use Symfony\Component\Routing\Router;
use Twig_Environment;

/**
 * Pattern: Singelton
 *
 * Class Model
 * @package AppBundle\Utils\ListRenderer
 */
class Model
{
    const Membre = 'Membre';
    const MembreEffectifs = 'MembreEffectifs';
    const MembreFraterie = 'MembreFraterie';
    const Attribution = 'Attribution';
    const Distinction = 'Distinction';
    const Creance = 'Creance';
    const Facture = 'Facture';

    /**
     * @var Model
     * @access private
     * @static
     */
    private static $_instance = null;

    private function __construct() { }

    /**
     * Méthode qui créé l'unique instance de la classe
     * si elle n'existe pas encore puis la retourne.
     *
     * @param void
     * @return Model
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Model();
        }
        return self::$_instance;
    }
}


/**
 * Cette class est un service disponible dans chaque controller.
 * Cela permet d'appeler tout les liste déjà écrites rapidement.
 *
 * Class ListContainer
 * @package AppBundle\Utils\ListRenderer
 */
class ListContainer
{

    /** @var Twig_Environment */
    private $twig;

    private $definedModels;

    private $router;

    public function __construct(\Twig_Environment $twig, Router $router)
    {
        $this->twig = $twig;
        $this->router = $router;
        $this->definedModels = Model::getInstance();
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

    public function getModel($type, $items)
    {
        switch ($type) {
            case Model::Membre:
                return ListModelsMembre::getDefault($this->twig, $this->router, $items);
                break;

            case Model::MembreEffectifs:
                return ListModelsMembre::getEffectifs($this->twig, $this->router, $items);
                break;

            case Model::MembreFraterie:
                return ListModelsMembre::getFraterie($this->twig, $this->router, $items);
                break;

            case Model::Attribution:
                return ListModelsAttributions::getDefault($this->twig, $this->router, $items);
                break;

            case Model::Distinction:
                return ListModelsDistinctions::getDefault($this->twig, $this->router, $items);
                break;

            case Model::Creance:
                return ListModelsCreances::getDefault($this->twig, $this->router, $items);
                break;

            case Model::Facture:
                return ListModelsFactures::getDefault($this->twig, $this->router, $items);
                break;

        }
    }
}