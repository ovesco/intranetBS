<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Groupe;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class StatisticsController
 * @package AppBundle\Controller
 * @Route("/intranet/statistics")
 *
 */
class StatisticsController extends Controller
{
    /**
     * @Route("/")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $groupes = $this->getDoctrine()->getManager()->getRepository('AppBundle:Groupe')->findAll();

        return $this->render('AppBundle:Statistics:page_statistics.html.twig', array('groupes' => $groupes));
    }


    /**
     * @Route("/get_graph_ajax", options={"expose"=true})
     * @param Request $request
     * @return JsonResponse
     */
    public function getGraphicsAjaxAction(Request $request)
    {
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
            case 'effectifs_pie_charts':
                return $this->getEffectifsPieCharts($options);
            case 'evolution_effectifs':
                return $this->getEvolutionEffectifChart($options);
            default:
                return $this->getEffectifsPieCharts($options);
        }

    }

    private function getEffectifsPieCharts($options){

        $groupeRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:Groupe');


        $parentId = $options['groupe'];
        $nb_niveau = $options['nb_niveau'];

        $groupeParent = new Groupe();
        $nombreTotalMembre = null;

        if(($parentId == '') or ($parentId == null))
        {
            $groupes =$groupeRepo->findBy(array('parent'=>null));
            $groupeParent->setEnfants($groupes);
            foreach($groupes as $groupe)
            {
                $nombreTotalMembre = $nombreTotalMembre + $groupeRepo->findNumberOfMembreAtDateRecursive($groupe->getId());
            }

        }
        else
        {
            $groupeParent = $groupeRepo->find($parentId);
            $nombreTotalMembre = $groupeRepo->findNumberOfMembreAtDateRecursive($groupeParent->getId());
        }


        $series = array();
        $data = $this->getGroupeData($groupeRepo,$groupeParent,$nombreTotalMembre,0,$nb_niveau,$series);




        $graphData = array(

            'chart' => array(
                'type' => 'pie'
            ),
            'title' => array(
                'text'=> ($groupeParent->getNom() == null ? 'Groupes racines' : $groupeParent->getNom())
            ),




            'tooltip' => array(
                'pointFormat' => '<b>{point.percentage:.2f}%</b>',
            ),
            'plotOptions' => array(
                'pie' => array(
                    'shadow' => false,
                    'center' => array('50%','50%'),
                    'dataLabels' => array('enabled'=> false),

                )
            ),
            'series' =>   $data
        );

        return $graphData;
    }

    private function getGroupeData($groupeRepo, Groupe $groupeParent, $nbTotalMembre, $niveau, $niveauMax, $series, $color = null)
    {

        $niveauMax = $niveauMax - 1;

        for ($niv = 0; $niv <= $niveauMax; $niv++) {
            //on commence par crée les series pour chaque niveau
            if (!isset($series[$niv])) {
                $series[$niv]['name'] = $niv;
                $series[$niv]['size'] = ((100 / ($niveauMax + 1)) * ($niv + 1)) . '%';
                $series[$niv]['innerSize'] = ((100 / ($niveauMax + 1)) * ($niv)) . '%';
                $series[$niv]['data'] = array();
            }
        }


        if ($niveau <= $niveauMax) {

            $effectifDirect = $groupeRepo->findNumberOfMembreAtDate($groupeParent->getId());

            if ($color == null)
                $color = 'rgba(' . mt_rand(0, 255) . ',' . mt_rand(0, 255) . ',' . mt_rand(0, 255) . ', 1)';

            for ($niv = $niveau; $niv <= $niveauMax; $niv++) {

                //effectif direct du groupe.
                $data = array();
                $data['name'] = 'Eff. direct ' . $groupeParent->getNom() . ' (' . $effectifDirect . ' pers.)';
                $data['y'] = (($effectifDirect / $nbTotalMembre) * 100);
                $data['color'] = $color;
                array_push($series[$niv]['data'], $data);
            }


            $color = 'rgba(' . mt_rand(0, 255) . ',' . mt_rand(0, 255) . ',' . mt_rand(0, 255) . ', 1)';


            foreach ($groupeParent->getEnfants() as $enfant) {
                $effectif = $groupeRepo->findNumberOfMembreAtDateRecursive($enfant->getId());
                $data = array();
                $data['name'] = $enfant->getNom() . ' (' . $effectif . ' pers.)';
                $data['y'] = (($effectif / $nbTotalMembre) * 100);
                $data['color'] = $color;
                array_push($series[$niveau]['data'], $data);
            }

            foreach ($groupeParent->getEnfants() as $enfant) {
                $series = $this->getGroupeData($groupeRepo, $enfant, $nbTotalMembre, $niveau + 1, $niveauMax + 1, $series, $color);
            }
        }

        return $series;


    }

    private function getEvolutionEffectifChart($options)
    {
        $groupeRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:Groupe');

        /*
         * Récupération des options
         */
        $intervalFormat = $options['interval'];
        $periodeFormat =  $options['periode'];
        $parentId = $options['groupe'];
        $childsOptions= $options['childs'];


        $ids= array();

        if(($parentId == '') or ($parentId == null))
        {
            $groupes =$groupeRepo->findBy(array('parent'=>null));
            foreach($groupes as $groupe)
            {
                array_push($ids,$groupe->getId());
                if($childsOptions == 'with_childs'){
                    $idsChild = $groupeRepo->getArrayOfChildIdsRecursive($groupe->getId());
                    $ids = array_merge($ids,$idsChild);
                }
            }

        }
        else{
            array_push($ids,$parentId);

            if($childsOptions == 'with_childs'){
                $idsChild = $groupeRepo->getArrayOfChildIdsRecursive($parentId);
                $ids = array_merge($ids,$idsChild);
            }
        }






        $interval = new \DateInterval($intervalFormat);
        $intervalTotal = new \DateInterval($periodeFormat);
        $intervalTotal->invert = 1;
        $end = new \DateTime();

        $data = array();

        foreach($ids as $id)
        {
            $groupe = $groupeRepo->find($id);

            $arrayEffectif = array();

            $current = new \DateTime();
            $current->add($intervalTotal);

            while($end > $current)
            {
                $next = clone $current;
                $next->add($interval);

                $effectif = $groupeRepo->findNumberOfMembreAtDateRecursive($id,$current);
                array_push($arrayEffectif,array($current->getTimestamp()*1000,$effectif));

                $current = clone $next;
            }


            $groupeData = array(
                'name' => $groupe->getNom(),
                'data' => $arrayEffectif,
                'lineWidth' => 2,
                'marker' => array(
                    'enabled' => false
                ),
            );

            array_push($data,$groupeData);

        }




        $graphData = array(

            'chart' => array(
                'type' => 'spline'
            ),
            'title' => array(
                'text'=> 'Evolution des effectifs'
            ),
            'xAxis' => array(
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
            'tooltip' => array(
                'pointFormat' => '<b>{point.y} pers.</b>',
            ),


            'plotOptions' => array(
                'line' => array(
                    'lineWidth' => 4,
                    'marker' => array(
                        'enabled' => false
                    )
                )
            ),


            'series' => $data

        );

        return $graphData;
    }


    private $em;

    public function __construct(EntityManager $em){
        $this->em = $em;
    }

    public function getEvolutionRappels()
    {

        $factureRepo = $this->em->getRepository('InterneFinancesBundle:Facture');

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
        $periodeFormat =  $options['periode'];



        $interval = new \DateInterval($intervalFormat);
        $intervalTotal = new \DateInterval($periodeFormat);
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
