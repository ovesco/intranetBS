<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Famille;
use AppBundle\Entity\Membre;
use AppBundle\Form\Membre\MembreShowType;
use AppBundle\Search\Membre\MembreSearch;
use AppBundle\Search\Membre\MembreSearchType;
use AppBundle\Search\Mode;
use AppBundle\Utils\ListUtils\ListKey;
use AppBundle\Utils\ListUtils\ListStorage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils\Menu\Menu;
use AppBundle\Search\Membre\MembreRepository;

/**
 * Class MembreController
 * @package AppBundle\Controller
 * @Route("/intranet/membre")
 */
class MembreController extends Controller {


    const SEARCH_RESULTS = "session_results";

    /**
     * Affiche la page d'ajout de membre
     *
     * @Route("/add")
     * @Menu("Ajouter un membre",block="database",order=1, icon="add", expanded=true)
     * @param Request $request
     * @return Response
     * @Template("AppBundle:Membre:page_add.html.twig")
     */
    public function addAction(Request $request) {
        return array();
    }

    /**
     * @Route("/show/{membre}", options={"expose"=true}, requirements={"membre" = "\d+"})
     * @ParamConverter("membre", class="AppBundle:Membre")
     * @param Request $request
     * @param Membre $membre
     * @return Response
     * @Template("AppBundle:Membre:page_show.html.twig")
     */
    public function showAction(Request $request, Membre $membre) {

        $membreForm = $this->createForm(MembreShowType::class, $membre);

        return array(
            'membre'            => $membre,
            'listing'           => $this->get('listing'),
            'membreForm'        => $membreForm->createView(),
        );
    }

    /**
     * @Route("/show_pdf/{membre}", requirements={"membre" = "\d+"})
     * @ParamConverter("membre", class="AppBundle:Membre")
     * @param Request $request
     * @param Membre $membre
     * @return Response
     */
    public function toPdfAction(Request $request, Membre $membre)
    {

        $html = $this->render('@App/Membre/pdf_show.html.twig', array(
                'membre' => $membre,
                'listing' => $this->get('listing'),
            )
        );

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type' => 'application/pdf'
                // 'Content-Disposition' => 'attachment; filename="file.pdf"'
            )
        );
    }


    /**
     * Affiche la page permettant de lancer une recherche
     *
     * @Route("/search")
     * @Menu("Rechercher un membre",block="database",order=2, icon="search", expanded=true)
     * @Template("AppBundle:Membre:page_search.html.twig")
     */
    public function searchAction(Request $request)
    {

        $membreSearch = new MembreSearch();
        $membreForm = $this->createForm(new MembreSearchType(),$membreSearch);


        /** @var ListStorage $sessionContainer */
        $sessionContainer = $this->get('list_storage');
        $sessionContainer->setRepository(ListKey::MEMBRES_SEARCH_RESULTS,'AppBundle:Membre');

        $membreForm->handleRequest($request);

        if ($membreForm->isValid()) {

            $elasticaManager = $this->container->get('fos_elastica.manager');

            /** @var MembreRepository $repository */
            $repository = $elasticaManager->getRepository('AppBundle:Membre');

            $results = $repository->search($membreSearch);

            //$results = $this->container->get('app.search')->Membre($membreSearch);

            //get the search mode
            $mode = $membreForm->get("mode")->getData();
            switch($mode)
            {
                case Mode::INCLUDE_PREVIOUS: //include new results with the previous
                    $sessionContainer->addObjects(ListKey::MEMBRES_SEARCH_RESULTS,$results);
                    break;
                case Mode::EXCLUDE_PREVIOUS: //exclude new results to the previous
                    $sessionContainer->removeObjects(ListKey::MEMBRES_SEARCH_RESULTS,$results);
                    break;
                case Mode::STANDARD:
                default:
                    $sessionContainer->setObjects(ListKey::MEMBRES_SEARCH_RESULTS,$results);

            }

        }

        return array('membreForm'=>$membreForm->createView(),'list_key'=>ListKey::MEMBRES_SEARCH_RESULTS);
    }




}
