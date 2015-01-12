<?php

namespace Interne\FinancesBundle\Controller;

use Interne\FinancesBundle\Entity\Facture;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Interne\FinancesBundle\Entity\FactureRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManager;

/**
 * Class StatisticsController
 * @package Interne\FinancesBundle\Controller
 */
class StatisticsController extends Controller
{

    private $em;

    public function __construct(EntityManager $em){
        $this->em = $em;
    }

    public function getEvolutionRappels()
    {

        $factureRepo = $this->em ->getRepository('InterneFinancesBundle:Facture');

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

        return $graphData;


    }

    public function getCreancesEmisesRecues($options)
    {

        $creanceRepo = $this->em ->getRepository('InterneFinancesBundle:Creance');

        $intervalFormat = $options['interval'];
        $periodeFormat = $options['periode'];



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

        return $graphData;

    }

    public function getMontantEnAttente($options)
    {

        $creanceRepo = $this->em ->getRepository('InterneFinancesBundle:Creance');

        $intervalFormat = $options['interval'];
        $periodeFormat = $options['periode'];

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

        return $graphData;

    }


}
