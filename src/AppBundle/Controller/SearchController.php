<?php

namespace AppBundle\Controller;

use AppBundle\Search\MembreSearch;
use AppBundle\Search\MembreSearchType;
use AppBundle\Search\MembreRepository;
use AppBundle\Search\Mode;
use AppBundle\Utils\ListRenderer\ListContainer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils\ListRenderer\ListStorage;


/* Annotations */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Utils\Menu\Menu;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * Class SearchController
 * @package AppBundle\Controller
 * @Route("/intranet/search")
 *
 */
class SearchController extends Controller
{
    /**
     * Recherche depuis la bar de recherche du menu
     * Effectue une recherche complète parmi les membres, groupes et familles
     * Filtre ensuite les 4 premiers résultats par catégorie pour ne pas en avoir trop
     *
     * @Route("/layout", options={"expose"=true})
     * @param Request $request
     * @return JsonResponse
     */
    public function layoutAction(Request $request) {

        $pattern     = $request->query->get('pattern');
        $pattern     = "*" . $pattern . "*";

        $limit       = 4;
        $jsonParser  = $this->get('jsonParser');

        $membres     = $this->get('app.search.membre')->find($pattern,$limit);
        $familles    = $this->get('app.search.famille')->find($pattern,$limit);
        $groupes     = $this->get('app.search.groupe')->find($pattern,$limit);

        $returned    = array();
        $returned['results']['category1']['name'] = 'membres';
        $returned['results']['category2']['name'] = 'familles';
        $returned['results']['category3']['name'] = 'unités';

        foreach($membres as $k => $m)
            $returned['results']['category1']['results'][$k] = $jsonParser->toSemanticCategorySearch('membre', $m);

        foreach($familles as $k => $f)
            $returned['results']['category2']['results'][$k] = $jsonParser->toSemanticCategorySearch('famille', $f);

        foreach($groupes as $k => $g)
            $returned['results']['category3']['results'][$k] = $jsonParser->toSemanticCategorySearch('groupe', $g);

        //On peut ici rajouter des catégories, faut juste implémenter la méthodes de jsonParser et garder la même structure

        return new JsonResponse($returned);
    }

}
