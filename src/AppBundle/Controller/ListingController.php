<?php

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils\Listing\Lister;

/* Annotation */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Utils\Menu\Menu;

/**
 * Class ListingController
 * @package AppBundle\Controller
 * @Route("/listing")
 */
class ListingController extends Controller
{

    /**
     * Gébère la barre qui s'affiche dans chaque page
     * @Route("/generate-bar", name="listing_generate_top_bar", options={"expose"=true})
     * @Template("AppBundle:Listing:top_layout_listing.html.twig")
     */
    public function listingBarAction() {

        return array('listing' => $this->get('listing'));
    }

    /**
     * Retourne la liste des listes actuelles en json
     * @Route("/listes-as-json", name="listing_load_listes_as_json", options={"expose"=true})
     */
    public function loadListesAsJsonAction() {

        $rtn    = array();

        foreach($this->get('listing')->getListes() as $liste)
            $rtn[] = array(
                'token' => $liste->getToken(),
                'name'  => $liste->name,
                'size'  => $liste->getSize()
            );

        return new JsonResponse($rtn);
    }


    /**
     * Permet d'avoir une vue d'ensemble des listes dynamiques disponibles
     * @return Response la vue
     * @Route("/", name="listing_page")
     * @Menu("Listes personalisée",block="database",order=3,icon="list")
     */
    public function listingDashboardAction() {


        $listing = $this->get('listing');

        return $this->render('AppBundle:Listing:page_listing.html.twig', array(

            'listing' => $listing
        ));
    }

    /**
     * @param $token
     * @return Response
     * @Route("/view/liste/{token}", name="listing_view_liste_by_token", options={"expose"=true})
     */
    public function viewListe($token) {

        /** @var Lister $listing */
        $listing = $this->get('listing');
        $liste   = $listing->getByToken($token);

        return $this->render('AppBundle:Listing:listing_view_liste.html.twig', array('liste' => $liste));
    }


    /**
     * Permet de créer une liste
     * @return Response redirection vers la page du listing
     * @Route("/add/{name}", name="listing_add", options={"expose"=true})
     */
    public function addListeAction($name) {

        $listing = $this->get('listing');
        $listing->addListe($name);
        $listing->save();

        return new JsonResponse();
    }


    /**
     * Permet de supprimer une liste du listing
     * @param $token string le token de la liste
     * @return Response redirection vers la page du listing
     * @Route("/remove/{token}", name="listing_remove_liste", options={"expose"=true})
     */
    public function removeListeAction($token) {

        $listing = $this->get('listing');
        $listing->removeListeByToken($token);
        $listing->save();

        return new JsonResponse();
    }


    /**
     * Permet de supprimer des éléments du listing
     * @param $token string le token de la liste
     * @param $ids array les membres à virer
     * @return Response Json vide
     * @Route("/remove-members-by-id/{token}/{ids}", name="listing_remove_members_by_id", options={"expose"=true})
     */
    public function removeFromListingByIds($token, $ids) {

        $listing = $this->get('listing');
        $data    = explode(',', $ids);
        $listing->getByToken($token)->removeByIds($data);
        $listing->save();
        return new JsonResponse('');
    }


    /**
     * Permet d'ajouter des éléments du listing
     * @param $token string le token de la liste
     * @param $ids array les membres à ajouter
     * @return Response Json vide
     * @Route("/add-members-by-id/{token}/{ids}", name="listing_add_members_by_id", options={"expose"=true})
     */
    public function addToListingByIds($token, $ids) {

        $listing = $this->get('listing');
        $data    = explode(',', $ids);

        $listing->getByToken($token)->addByIds($data);
        $listing->save();
        return new JsonResponse('');
    }
}
