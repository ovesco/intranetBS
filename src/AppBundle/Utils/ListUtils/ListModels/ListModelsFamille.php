<?php

namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\Famille;
use AppBundle\Entity\Membre;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModel;
use AppBundle\Utils\ListUtils\ListModelInterface;
use AppBundle\Utils\ListUtils\ListRenderer;
use Symfony\Component\Routing\Router;

class ListModelsFamille extends  ListModel
{



    /**
     * @param $items
     * @param string $url
     * @return ListRenderer
     */
    public function getDefault( $items, $url = null)
    {
        $router = $this->router;
        $list = new ListRenderer($this->twig, $items);
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
     * @param $items
     * @param string $url
     * @return ListRenderer
     */
    public function getSearchResults($items, $url = null)
    {
        $twig = $this->twig;
        $router = $this->router;
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