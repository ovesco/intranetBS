<?php

namespace AppBundle\Controller;

use AppBundle\Utils\Menu\Menu;
use AppBundle\Entity\Creance;
use AppBundle\Form\Creance\CreanceAddType;
use AppBundle\Search\Creance\CreanceRepository as ElasticRepository;
use AppBundle\Search\Creance\CreanceSearch;
use AppBundle\Search\Creance\CreanceSearchType;
use AppBundle\Utils\ListUtils\ListModels\ListModelsCreances;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils\ListUtils\ListStorage;
use AppBundle\Utils\ListUtils\ListKey;
use AppBundle\Utils\Response\ResponseFactory;
use AppBundle\Repository\CreanceRepository;
use AppBundle\Entity\Debiteur;
use AppBundle\Search\Mode;
use AppBundle\Utils\Finances\Facturation;

/**
 * Class CreanceController
 * @package AppBundle\Controller
 * @Route("/intranet/creance")
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
     * @Route("/remove/{creance}", options={"expose"=true})
     * @param Creance $creance
     * @ParamConverter("creance", class="AppBundle:Creance")
     * @param Request $request
     * @return Response
     */
    public function removeAction(Request $request,Creance $creance)
    {
        $this->denyAccessUnlessGranted('remove',$creance);
        /*
         * On vérifie que la cérance n'est pas liée à une facture avant de la supprimer
         */
        if(!$creance->isFactured()) {

            /** @var CreanceRepository $repo */
            $repo = $this->get('app.repository.creance');
            $repo->remove($creance);
            return ResponseFactory::ok();
        }
        return ResponseFactory::conflict('Impossible de supprimer une créance facturée');
    }


    /**
     * @Route("/create/{debiteur}", options={"expose"=true})
     * @param Request $request
     * @param Debiteur $debiteur
     * @ParamConverter("debiteur", class="AppBundle:Debiteur")
     * @return Response
     * @Template("AppBundle:Creance:form_modal.html.twig")
     */
    public function createAction(Request $request,Debiteur $debiteur)
    {
        $creance = new Creance();
        $this->denyAccessUnlessGranted('create',$creance);

        $creance->setDebiteur($debiteur);

        $form = $this->createForm(new CreanceAddType(),$creance,
            array('action' => $this->generateUrl('app_creance_create',array('debiteur'=>$debiteur->getId()))));

        $form->handleRequest($request);

        if($form->isValid())
        {
            $creance->setDateCreation(new \DateTime('now'));
            $this->get('app.repository.creance')->save($creance);
            return ResponseFactory::ok();
        }

        return array('form'=>$form->createView());


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

        $this->denyAccessUnlessGranted('view',$creance);
        return array('creance' => $creance);

    }

    /**
     * @Route("/search", options={"expose"=true})
     * @Menu("Recherche de créances",block="finances",order=1,icon="search")
     * @param Request $request
     * @return Response
     * @Template("AppBundle:Creance:page_recherche.html.twig")
     *
     *
     */
    public function searchAction(Request $request){

        $creanceSearch = new CreanceSearch();

        $searchForm = $this->createForm(new CreanceSearchType(), $creanceSearch);

        /** @var ListStorage $sessionContainer */
        $sessionContainer = $this->get('list_storage');
        $sessionContainer->setRepository(ListKey::CREANCES_SEARCH_RESULTS,'AppBundle:Creance');


        $searchForm->handleRequest($request);
        if ($searchForm->isValid()) {


            $elasticaManager = $this->container->get('fos_elastica.manager');

            /** @var ElasticRepository $repository */
            $repository = $elasticaManager->getRepository('AppBundle:Creance');

            $results = $repository->search($creanceSearch);

            //get the search mode
            $mode = $searchForm->get("mode")->getData();
            switch($mode)
            {
                case Mode::MODE_INCLUDE: //include new results with the previous
                    $sessionContainer->addObjects(ListKey::CREANCES_SEARCH_RESULTS,$results);
                    break;
                case Mode::MODE_EXCLUDE: //exclude new results to the previous
                    $sessionContainer->removeObjects(ListKey::CREANCES_SEARCH_RESULTS,$results);
                    break;
                case Mode::MODE_STANDARD:
                default:
                    $sessionContainer->setObjects(ListKey::CREANCES_SEARCH_RESULTS,$results);

            }

        }

        return array('searchForm'=>$searchForm->createView(),
                'list_key'=>ListKey::CREANCES_SEARCH_RESULTS);
    }

    /**
     * @Route("/facturation/{list_session_key}", options={"expose"=true})
     * @param Request $request
     * @return Response
     *
     */
    public function facturationAction(Request $request, $list_session_key){

        /** @var ListStorage $sessionContainer */
        $sessionContainer = $this->get('list_storage');

        switch($list_session_key)
        {
            case ListKey::CREANCES_SEARCH_RESULTS:
                $sessionContainer->setRepository(ListKey::CREANCES_SEARCH_RESULTS,'AppBundle:Creance');

                $creances = $sessionContainer->getObjects(ListKey::CREANCES_SEARCH_RESULTS);

                /** @var Facturation $facturation */
                $facturation = $this->get('app.facturation');
                $facturation->facturationCreances(new ArrayCollection($creances));


                return ResponseFactory::ok();
            default:
                return ResponseFactory::badRquest($list_session_key.' is not implemented for CreanceController::facturationActino');
        }




    }



}