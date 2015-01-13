<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListingController extends Controller
{
    /**
     * Permet d'avoir une vue d'ensemble des listes dynamiques disponibles
     * @return Response la vue
     * @route("listing/vue-globale", name="listing_vue_globale")
     */
    public function listingDashboardAction() {


        $listing = $this->get('listing');

        return $this->render('Listing/full_listing_view.html.twig', array(

            'listing' => $listing
        ));
    }


    /**
     * Permet de créer une liste
     * @param Request $request
     * @return Response redirection vers la page du listing
     * @route("listing/add-liste", name="listing_creer_liste")
     */
    public function addListeAction(Request $request) {

        $listing = $this->get('listing');
        $name    = $request->request->get('new_liste_name');
        $listing->addListe($name);
        $listing->save();

        return $this->redirect($this->generateUrl('listing_vue_globale'));
    }


    /**
     * Permet de supprimer une liste du listing
     * @param $token string le token de la liste
     * @return Response redirection vers la page du listing
     * @route("listing/remove-liste/{token}", name="listing_supprimer_liste", options={"expose"=true})
     */
    public function removeListeAction($token) {

        $listing = $this->get('listing');
        $listing->removeListeByToken($token);
        $listing->save();

        return $this->redirect($this->generateUrl('listing_vue_globale'));
    }


    /**
     * Permet de supprimer des éléments du listing
     * @param $token string le token de la liste
     * @param $ids array les membres à virer
     * @return Response Json vide
     * @route("listing/remove-membres-id/{token}/{ids}", name="listing_supprimer_membres_par_id", options={"expose"=true})
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
     * @route("listing/add-membres-id/{token}/{ids}", name="listing_ajouter_membres_par_id", options={"expose"=true})
     */
    public function addToListingByIds($token, $ids) {

        $listing = $this->get('listing');
        $data    = explode(',', $ids);
        $listing->getByToken($token)->addByIds($data);
        $listing->save();
        return new JsonResponse('');
    }
}
