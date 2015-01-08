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
            $options = $request->request->get('options');



            $graphData = $this->getGraphData($idGraph,$options);

            return new JsonResponse($graphData);

        }
        return new JsonResponse();
    }

    private function getGraphData($idGraph,$options)
    {
        $em = $this->getDoctrine()->getManager();
        $factureRepo = $em->getRepository('InterneFinancesBundle:Facture');
        $creanceRepo = $em->getRepository('InterneFinancesBundle:Creance');


        $graphData = null;

        switch($idGraph){
            /*
             * Affiche le nombre de facture ouverte en fonction du nombre de rappels.
             */
            case 0:

                $data = $factureRepo->getNombreRappelArray();

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
                        'categories' => $categories
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
            /*
             * Affiche le montant des facture ouverte en fonction du temps
             */
            case 1:

                $intervalFormat = $options['graph_1_interval'];
                $periodeFormat = $options['graph_1_periode'];



                $interval = new \DateInterval($intervalFormat);//une semaine
                $intervalTotal = new \DateInterval($periodeFormat);//une année
                $intervalTotal->invert = 1;
                $end = new \DateTime();

                $arrayMontantEmis = array();
                $arrayMontantRecu = array();


                $current = new \DateTime();
                $current->add($intervalTotal);

                while($end > $current)
                {
                    $next = clone $current;
                    $next->add($interval);

                    $montantEmis = $creanceRepo->getMontantEmisBetweenDates($current,$next);
                    $montantRecu = $creanceRepo->getMontantRecuBetweenDates($current,$next);

                    array_push($arrayMontantEmis,array($next->getTimestamp()*1000,$montantEmis));
                    array_push($arrayMontantRecu,array($next->getTimestamp()*1000,$montantRecu));


                    $current = clone $next;
                }

                $graphData = array(

                    'chart' => array(
                        'type' => 'spline'
                    ),
                    'title' => array(
                        'text'=> 'Créances émises et reçues'
                    ),
                    'xAxis' => array(
                        //'categories' => $arrayDate,
                        'type'=>'datetime',
                        'labels'=>array(
                            'overflow' => 'justify'
                            )

                    ),
                    'yAxis' => array(
                        'title' => array(
                            'text' => 'Montant'
                        ),
                        'min' => 0
                    ),


                    'plotOptions' => array(
                        'line' => array(
                            'lineWidth' => 4,
                            'marker' => array(
                                'enabled' => false
                            )
                        )
                    ),


                    'series' => array(
                        array(
                            'name' => 'Créances émises',
                            'data' => $arrayMontantEmis,
                            'lineWidth' => 2,
                            'marker' => array(
                                'enabled' => false
                            ),
                        ),

                        array(
                            'name' => 'Créances reçu',
                            'data' => $arrayMontantRecu,
                            'lineWidth' => 2,
                            'marker' => array(
                                'enabled' => false
                            ),
                        )
                    )

                );

                break;

            /*
             * Affiche le montant payé en fonction du temps
             */
            case 2:

                $intervalFormat = $options['graph_2_interval'];
                $periodeFormat = $options['graph_2_periode'];

                $interval = new \DateInterval($intervalFormat);//une semaine
                $intervalTotal = new \DateInterval($periodeFormat);//une année
                $intervalTotal->invert = 1;
                $end = new \DateTime();

                $arrayMontantEmis = array();



                $current = new \DateTime();
                $current->add($intervalTotal);

                while($end > $current)
                {
                    $next = clone $current;
                    $next->add($interval);

                    $montantEmis = $creanceRepo->getMontantOuvertAtDate($next);


                    array_push($arrayMontantEmis,array($next->getTimestamp()*1000,$montantEmis));



                    $current = clone $next;
                }

                $graphData = array(

                    'chart' => array(
                        'type' => 'spline'
                    ),
                    'title' => array(
                        'text'=> 'Montant en attente de payement'
                    ),
                    'xAxis' => array(
                        //'categories' => $arrayDate,
                        'type'=>'datetime',
                        'labels'=>array(
                            'overflow' => 'justify'
                        )

                    ),
                    'yAxis' => array(
                        'title' => array(
                            'text' => 'Montant'
                        ),
                        'min' => 0
                    ),


                    'plotOptions' => array(
                        'line' => array(
                            'lineWidth' => 4,
                            'marker' => array(
                                'enabled' => false
                            )
                        )
                    ),


                    'series' => array(
                        array(
                            'name' => 'Créances non payée',
                            'data' => $arrayMontantEmis,
                            'lineWidth' => 2,
                            'marker' => array(
                                'enabled' => false
                            ),
                        ),


                    )

                );

                break;


        }



        return $graphData;
    }
}
