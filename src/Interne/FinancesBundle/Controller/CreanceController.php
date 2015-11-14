<?php

namespace Interne\FinancesBundle\Controller;

use AppBundle\Entity\Groupe;
use AppBundle\Utils\Listing\Liste;
use AppBundle\Utils\Listing\Lister;
use AppBundle\Utils\Menu\Menu;
use Interne\FinancesBundle\Entity\Creance;
use Interne\FinancesBundle\Form\CreanceAddType;
use Interne\FinancesBundle\Search\CreanceRepository;
use Interne\FinancesBundle\Search\CreanceSearch;
use Interne\FinancesBundle\Search\CreanceSearchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils\ListUtils\ListStorage;
use AppBundle\Utils\ListUtils\ListContainer;


/**
 * Class CreanceController
 * @package Interne\FinancesBundle\Controller
 * @Route("/creance")
 */
class CreanceController extends Controller
{

    const SEARCH_RESULTS_LIST = "creance_search_results";

    /**
     *
     * Supprime une cérance.
     * Ne supprime que les cérances qui sont pas
     * liée a une facture.
     *
     *
     * @Route("/delete/{creance}", options={"expose"=true})
     * @param Creance $creance
     * @ParamConverter("creance", class="InterneFinancesBundle:Creance")
     * @param Request $request
     * @return Response
     */
    public function deleteAction(Request $request,Creance $creance)
    {
        /*
         * On vérifie que la cérance n'est pas liée à une facture avant de la supprimer
         */
        if(!$creance->isFactured()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($creance);
            $em->flush();

            $response = new Response();
            return $response->setStatusCode(200); // OK
        }
        $response = new Response();
        return $response->setStatusCode(409, "Impossible de supprimer une créance facturée, supprimez d'abord la facture"); // Conflict
    }


    /**
     * @Route("/show/{creance}", options={"expose"=true})
     * @param Creance $creance
     * @ParamConverter("creance", class="InterneFinancesBundle:Creance")
     * @param Request $request
     * @Template("InterneFinancesBundle:Creance:show_modal.html.twig")
     * @return Response
     */
    public function showAction(Request $request,Creance $creance){

        return array('creance' => $creance);

    }

    /**
     * @Route("/search", options={"expose"=true})
     * @Menu("Recherche de créances",block="finances",order=1,icon="search")
     * @param Request $request
     * @return Response
     * @Template("InterneFinancesBundle:Creance:page_recherche.html.twig")
     */
    public function searchAction(Request $request){

        $creanceSearch = new CreanceSearch();

        $searchForm = $this->createForm(new CreanceSearchType(), $creanceSearch);

        $results = array();

        $searchForm->handleRequest($request);


        if ($searchForm->isValid()) {

            $creanceSearch = $searchForm->getData();

            $elasticaManager = $this->container->get('fos_elastica.manager');

            /** @var CreanceRepository $repository */
            $repository = $elasticaManager->getRepository('InterneFinancesBundle:Creance');

            $results = $repository->search($creanceSearch);

            /** @var ListStorage $sessionContainer */
            $sessionContainer = $this->get('list_storage');
            $sessionContainer->setRepository(CreanceController::SEARCH_RESULTS_LIST,'InterneFinancesBundle:Creance');
            $sessionContainer->setModel(CreanceController::SEARCH_RESULTS_LIST,ListContainer::CreanceSearchResults);
            $sessionContainer->setObjects(CreanceController::SEARCH_RESULTS_LIST,$results);

        }

        return array('searchForm'=>$searchForm->createView(),
                'list_key'=>CreanceController::SEARCH_RESULTS_LIST);
    }
}