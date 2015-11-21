<?php

namespace AppBundle\Controller;

use AppBundle\Utils\Menu\Menu;
use AppBundle\Entity\Creance;
use AppBundle\Form\Creance\CreanceAddType;
use AppBundle\Search\Creance\CreanceRepository;
use AppBundle\Search\Creance\CreanceSearch;
use AppBundle\Search\Creance\CreanceSearchType;
use AppBundle\Utils\ListUtils\ListModels\ListModelsCreances;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils\ListUtils\ListStorage;
use AppBundle\Utils\ListUtils\ListKey;


/**
 * Class CreanceController
 * @package AppBundle\Controller
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
     * @ParamConverter("creance", class="AppBundle:Creance")
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
     * @ParamConverter("creance", class="AppBundle:Creance")
     * @param Request $request
     * @Template("AppBundle:Creance:show_modal.html.twig")
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
     * @Template("AppBundle:Creance:page_recherche.html.twig")
     */
    public function searchAction(Request $request){

        $creanceSearch = new CreanceSearch();

        $searchForm = $this->createForm(new CreanceSearchType(), $creanceSearch);

        /** @var ListStorage $sessionContainer */
        $sessionContainer = $this->get('list_storage');
        $sessionContainer->setRepository(ListKey::CREANCES_SEARCH_RESULTS,'AppBundle:Creance');


        $searchForm->handleRequest($request);
        if ($searchForm->isValid()) {

            $creanceSearch = $searchForm->getData();

            $elasticaManager = $this->container->get('fos_elastica.manager');

            /** @var CreanceRepository $repository */
            $repository = $elasticaManager->getRepository('AppBundle:Creance');

            $results = $repository->search($creanceSearch);

            //set results in session
            $sessionContainer->setObjects(ListKey::CREANCES_SEARCH_RESULTS,$results);

        }

        return array('searchForm'=>$searchForm->createView(),
                'list_key'=>ListKey::CREANCES_SEARCH_RESULTS);
    }
}