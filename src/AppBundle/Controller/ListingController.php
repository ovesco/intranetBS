<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Listing;
use AppBundle\Entity\Membre;
use AppBundle\Utils\Response\ResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils\Listing\Lister;
use AppBundle\Form\Listing\ListingType;

/* Annotation */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Utils\Menu\Menu;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class ListingController
 * @package AppBundle\Controller
 * @Route("/intranet/listing")
 */
class ListingController extends Controller
{

    /**
     * Permet d'avoir une vue d'ensemble des listes dynamiques disponibles
     * @return Response la vue
     * @Route("")
     * @Menu("Listes personalisée",block="database",order=3,icon="list")
     * @Template("AppBundle:Listing:page_listing.html.twig")
     */
    public function myListingAction(Request $request) {


        $listing = $this->get('app.repository.listing')->listingOfUser($this->getUser());

        return  array('listing' => $listing);
    }

    /**
     * @return Response la vue
     * @Route("/my_listing_modal")
     * @Template("AppBundle:Listing:my_listing_modal.html.twig")
     */
    public function myListingModalAction(Request $request) {

        $listing = $this->get('app.repository.listing')->listingOfUser($this->getUser());
        return  array('listing' => $listing);
    }

    /**
     * @return Response la vue
     * @Route("/create")
     * @Template("AppBundle:Listing:form_modal.html.twig")
     */
    public function createAction(Request $request) {

        $listing = new Listing();

        $form = $this->createForm(ListingType::class,$listing,array('action' => $this->generateUrl('app_listing_create')));

        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted())
        {
            $listing->setUser($this->getUser());
            $this->get('app.repository.listing')->save($listing);
            return ResponseFactory::ok();
        }

        return  array('form' => $form->createView());
    }

    /**
     * @return Response
     * @Route("/update/{listing}")
     * @Template("AppBundle:Listing:form_modal.html.twig")
     * @ParamConverter("listing", class="AppBundle:Listing")
     */
    public function updateAction(Request $request, Listing $listing) {

        $form = $this->createForm(ListingType::class,$listing,array('action' => $this->generateUrl('app_listing_update')));

        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted())
        {
            $this->get('app.repository.listing')->save($listing);
            return ResponseFactory::ok();
        }

        return  array('form' => $form->createView());
    }


    /**
     * @return Response la vue
     * @Route("/show/{listing}")
     * @Template("AppBundle:Listing:page_show.html.twig")
     * @ParamConverter("listing", class="AppBundle:Listing")
     */
    public function showAction(Request $request, Listing $listing) {

        return  array('listing' => $listing);
    }

    /**
     * @return Response la vue
     * @Route("/show/{listing}")
     * @Template("AppBundle:Listing:show_modal.html.twig")
     * @ParamConverter("listing", class="AppBundle:Listing")
     */
    public function showModalAction(Request $request, Listing $listing) {

        return  array('listing' => $listing);
    }




    /**
     * @return Response la vue
     * @Route("/manage_button/{entityClass}/{entityId}")
     */
    public function manageButtonAction(Request $request,$entityClass,$entityId) {

        $listings = array();
        switch($entityClass)
        {
            case 'membre':
                $listings = $this->get('app.repository.listing')->listingOfUser($this->getUser(),Membre::class);
        }

        $json = array();
        /** @var Listing $list */
        foreach($listings as $list)
        {
            $json[] = array('name'=>$list->getName());
        }

        return new JsonResponse($json);
    }




}
