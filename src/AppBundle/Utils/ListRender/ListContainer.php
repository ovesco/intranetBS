<?php

namespace AppBundle\Utils\ListRender;

use Twig_Environment;

/**
 * Cette class est un service disponible dans chaque controller.
 * Cela permet d'appeler tout les liste dàjà écrite rapidement.
 *
 * Class ListContainer
 * @package AppBundle\Utils\ListRender
 */
class ListContainer {

    /** @var Twig_Environment */
    private $twig;

    public function __construct(Twig_Environment $twig){
        $this->twig = $twig;
    }

    /**
     * Retourne une liste vierge.
     *
     * @return ListRender
     */
    public function getNewListRender(){
        return new ListRender($this->twig);
    }

}