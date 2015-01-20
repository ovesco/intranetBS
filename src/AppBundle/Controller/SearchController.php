<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SearchController extends Controller
{
    /**
     * @Route("search", name="interne_search")
     */
    public function indexAction()
    {
        return $this->render('Search/search.html.twig');
    }

    /**
     * @Route("search/membres/ajax/{pattern}", name="interne_search_membres_ajax", options={"expose"=true})
     */
    public function searchMembres($pattern = "") {
        $finder = $this->container->get('fos_elastica.finder.search.membre');

        // Option 1. Returns all users who have example.net in any of their mapped fields
        $membres = $finder->find($pattern);

        return $this->render('Membre/liste.html.twig', array(
            'membres' => $membres
        ));
    }

    /**
     * @Route("search/familles/ajax/{pattern}", name="interne_search_familles_ajax", options={"expose"=true})
     */
    public function searchFamilles($pattern = "") {
        $finder = $this->container->get('fos_elastica.finder.search.famille');

        // Option 1. Returns all users who have example.net in any of their mapped fields
        $familles = $finder->find($pattern);

        return $this->render('Famille/liste.html.twig', array(
            'familles' => $familles
        ));
    }

    /**
     * @Route("search/groupes/ajax/{pattern}", name="interne_search_groupes_ajax", options={"expose"=true})
     */
    public function searchGroupes($pattern = "") {
        $finder = $this->container->get('fos_elastica.finder.search.groupe');

        // Option 1. Returns all users who have example.net in any of their mapped fields
        $groupes = $finder->find($pattern);

        return $this->render('Groupe/liste.html.twig', array(
            'groupes' => $groupes
        ));
    }
}
