<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/* Annotations */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Utils\Menu\Menu;


/**
 * Class SearchController
 * @package AppBundle\Controller
 * @Route("/search")
 */
class SearchController extends Controller
{
    /**
     * Affiche la page permettant de lancer une recherche
     * @Route("/search", name="interne_search")
     * @Menu("Rechercher",block="database",order=2, icon="search", expanded=true)
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Search:search.html.twig');
    }

    /**
     * Effectue une recherche complète parmi les membres, groupes et familles
     * Filtre ensuite les 4 premiers résultats par catégorie pour ne pas en avoir trop
     * @Route("/layout-search", name="interne_main_layout_search", options={"expose"=true})
     * @param Request $request
     * @return JsonResponse
     */
    public function layoutSearchAction(Request $request) {

        $pattern     = $request->query->get('pattern');
        $pattern     = "*" . $pattern . "*";


        $limit       = 4;
        $jsonParser  = $this->get('jsonParser');

        $membres     = array_slice($this->searchMembres($pattern), 0, $limit);
        $familles    = array_slice($this->searchFamilles($pattern), 0, $limit);
        $groupes     = array_slice($this->searchGroupes($pattern), 0, $limit);
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

    /**
     * Effectue une recherche sur les membres pour un pattern donné
     * @param string $pattern
     * @return array
     */
    private function searchMembres($pattern)
    {

        return $this->get('fos_elastica.finder.search.membre')->find($pattern);
    }

    /**
     * Effectue une recherche sur les familles pour un pattern donné
     * @param string $pattern
     * @return array
     */
    private function searchFamilles($pattern)
    {

        return $this->get('fos_elastica.finder.search.famille')->find($pattern);
    }

    /**
     * Effectue une recherche sur les groupes pour un pattern donné
     * @param string $pattern
     * @return array
     */
    private function searchGroupes($pattern)
    {

        return $this->get('fos_elastica.finder.search.groupe')->find($pattern);
    }

    /**
     * Appelée par la page de recherche, permet de lancer une recherche plus poussée sur un objet particulier
     * La méthode appelle ensuite une des fonctions de recherche atitrée, génère le tableau correspondant parmi les vues
     * et le retourne. Ca permet d'éviter d'avoir à sérializer des données en Json et tout
     * @param Request $request
     * @return Response
     * @Route("/advanced-search", name="interne_search_advanced", options={"expose"=true})
     */
    public function advancedSearchAction(Request $request) {

        $type       = $request->request->get('type');
        $pattern    = "*" . $request->request->get('pattern') . "*";
        $method     = 'search' . ucfirst($type);
        //$jsonParser = $this->get('jsonParser');
        $results    = $this->$method($pattern);
        //$returned   = array();

        return $this->render('@App/Membre/liste.html.twig', array('membres' => $results));

        /*
        foreach($results as $entity) {

            if($type == 'membres') $returned[]          = $jsonParser->simplifyMembre($entity);
            else if($type == 'familles') $returned[]    = $jsonParser->simplifyFamille($entity);
            else if($type == 'groupes') $returned[]     = $jsonParser->simplifyGroupe($entity);
        }
        */

        //return new JsonResponse($returned);
    }

    /**
     * Recherche les familles pour le formulaire d'ajout de membre
     * @Route("/famille-search", name="interne_famille_search", options={"expose"=true})
     * @param Request $request
     * @return JsonResponse
     */
    public function AddMembreSearchFamille(Request $request){

        $pattern     = $request->query->get('pattern');
        $pattern     = "*" . $pattern . "*";


        $limit       = 4;
        $jsonParser  = $this->get('jsonParser');

        $familles    = array_slice($this->searchFamilles($pattern), 0, $limit);

        $returned    = array();
        foreach($familles as $k => $f)
            $returned['results'][] = $jsonParser->toSelectFamille($f);

        return new Response($this->get('jms_serializer')->serialize($returned,'json'));//JsonResponse($returned);

    }
}
