<?php

namespace AppBundle\Controller;

use Interne\FinancesBundle\Entity\Facture;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Interne\FinancesBundle\Entity\FactureRepository;
use Symfony\Component\HttpFoundation\JsonResponse;



/**
 * Class StatisticsController
 * @package AppBundle\Controller
 * @Route("/statistics")
 *
 */
class StatisticsController extends Controller
{
    /**
     * @Route("/", name="app_bundle_statistics")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Statistics:panel.html.twig');
    }


    /**
     * @Route("/get_graph_ajax", name="interne_fiances_statistics_get_graph", options={"expose"=true})
     *
     * @return JsonResponse
     */
    public function getGraphicsAjaxAction()
    {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {

            $idGraph = $request->request->get('idGraph');
            $options = $request->request->get('options');

            $graphData = $this->getGraphData($idGraph,$options);

            return new JsonResponse($graphData);

        }
        return new JsonResponse();
    }




    private function getGraphData($idGraph,$options)
    {

        $financesController = $this->get('finances_statistics');

        switch($idGraph)
        {
            case 'finances_evolution_rappels':
                return $financesController->getEvolutionRappels();
            case 'creances_emises_recues':
                return $financesController->getCreancesEmisesRecues($options);
            case 'montant_en_attente':
                return $financesController->getMontantEnAttente($options);

        }




    }
}
