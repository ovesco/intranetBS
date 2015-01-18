<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SearchController extends Controller
{
    /**
     * @Route("search", name="interne_search")
     * @Route("search/{pattern}", name="interne_search_pattern")
     */
    public function indexAction($pattern = "")
    {
        $finder = $this->container->get('fos_elastica.finder.search.membre');

        // Option 1. Returns all users who have example.net in any of their mapped fields
        $results = $finder->find($pattern);

        return $this->render('Search/search.html.twig', array(
            'results' => $results
        ));
    }

    /**
     * @Route("search/ajax/pattern", name="interne_search_ajax_pattern", options={"expose"=true})
     */
    public function search($pattern) {
        $finder = $this->container->get('fos_elastica.finder.search.membre');

        // Option 1. Returns all users who have example.net in any of their mapped fields
        $results = $finder->find($pattern);

        return $results;
    }
}
