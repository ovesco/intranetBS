<?php

namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\Famille;
use AppBundle\Entity\Membre;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModelInterface;
use AppBundle\Utils\ListUtils\ListRenderer;
use Symfony\Component\Routing\Router;

class ListModelsFamille implements ListModelInterface
{

    static public function getRepresentedClass(){
        return 'AppBundle\Entity\Famille';
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

        $list->addColumn(new Column('Nom', function (Famille $famille) use ($router) {
            return '<a href="' . $router->generate('app_famille_show', array('famille' => $famille->getId())) . '">' . $famille->getNom() . '</a>';
        }));

        $list->addColumn(new Column('Enfants', function (Famille $famille) {
            $childs = '';
            /** @var Membre $child */
            foreach($famille->getMembres() as $child)
            {
                $childs = $childs.$child->getPrenom().'<br>';
            }
            return $childs;
        }));


        return $list;
    }

    /**
     * @param \Twig_Environment $twig
     * @param Router $router
     * @param $items
     * @param string $url
     * @return ListRenderer
     */
    static public function getSearchResults(\Twig_Environment $twig, Router $router, $items, $url = null)
    {
        $list = new ListRenderer($twig, $items);
        $list->setUrl($url);

        $list->setSearchBar(true);

        $list->addColumn(new Column('Nom', function (Famille $famille) use ($router) {
            return '<a href="' . $router->generate('app_famille_show', array('famille' => $famille->getId())) . '">' . $famille->getNom() . '</a>';
        }));

        $list->addColumn(new Column('Enfants', function (Famille $famille) {
            $childs = '';
            /** @var Membre $child */
            foreach($famille->getMembres() as $child)
            {
                $childs = $childs.$child->getPrenom().'<br>';
            }
            return $childs;
        }));


        return $list;
    }


}


?>