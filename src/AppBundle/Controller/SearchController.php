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

    public function search() {

    }
}
