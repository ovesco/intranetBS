<?php

namespace Interne\FinancesBundle\Controller;

use Interne\FinancesBundle\Entity\Facture;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Interne\FinancesBundle\Entity\FactureRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class StatisticsController
 * @package Interne\FinancesBundle\Controller
 * @Route("/statistics")
 */
class StatisticsController extends Controller
{
    /**
     * @Route("/", name="interne_fiances_statistics")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {


        return $this->render('InterneFinancesBundle:Statistics:panel.html.twig');
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

            $graphData = $this->getGraphData($idGraph);

            return new JsonResponse($graphData);

        }
        return new JsonResponse();
    }

    private function getGraphData($idGraph)
    {
        $em = $this->getDoctrine()->getManager();
        $factureRepo = $em->getRepository('InterneFinancesBundle:Facture');


        $graphData = null;

        switch($idGraph){
            case 0:

                $facture = new Facture();

                /*
                 * Uniquement facture ouverte
                 */
                $facture->setStatut('ouverte');
                /*
                 * mise a null pour la fonction de recherche
                 */
                $facture->setDatePayement(null);
                $facture->setMontantRecu(null);

                $factures= $factureRepo->findBySearch($facture);

                $maxNombreRappel = -1;
                $data = array();

                foreach($factures as $facture)
                {
                    $nombreRappel = $facture->getNombreRappels();

                    if($nombreRappel>$maxNombreRappel)
                    {
                        for($i = $maxNombreRappel+1; $i <= $nombreRappel; $i++)
                        {
                            $data[$i] = 0;
                        }

                        $maxNombreRappel = $nombreRappel;
                    }
                    $data[$nombreRappel]++;
                }

                $categories = [];
                for($i = 0; $i < count($data); $i++)
                {
                    array_push($categories,$i);
                }

                $graphData = array(
                    'chart' => array(
                        'type' => 'column',
                    ),
                    'title' => array(
                        'text'=> 'Evolution des rappels'
                    ),
                    'subtitle' => array(
                        'text'=> 'Basé sur les factures ouvertes'
                    ),
                    'xAxis' => array(
                        'title' => array(
                            'text'=> 'Nombres de Rappels'
                        ),
                        'categories' => array($categories)
                    ),
                    'yAxis' => array(
                        'title' => array(
                            'text'=> 'Nombres de factures'
                        ),
                        'min' => 0,

                    ),
                    'series' => array(
                        array(
                            'name' => 'Données',
                            'data' => $data,
                        )
                    ),
                    'plotOptions' => array(
                        'column' => array(
                            'pointPadding' => 0.2,
                            'borderWidth' => 0
                        )
                    )
                );

                break;
            case 1:

                $graphData = array(
                    'chart' => array(
                        'type' => 'bubble',
                        'zoomType' => 'xy'
                    ),
                    'title' => array(
                        'text'=> 'Highcharts Bubbles'
                    )

                );

                break;


        }



        return $graphData;
    }
}
