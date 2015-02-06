<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Groupe;



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
        $groupes = $this->getDoctrine()->getManager()->getRepository('AppBundle:Groupe')->findAll();

        return $this->render('AppBundle:statistics:page_statistics.html.twig',array('groupes'=>$groupes));
    }


    /**
     * @Route("/get_graph_ajax", name="interne_fiances_statistics_get_graph", options={"expose"=true})
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

    private function getGroupeData($groupeRepo,Groupe $groupeParent, $nbTotalMembre,$niveau,$niveauMax,$series,$color = null)
    {

        $niveauMax = $niveauMax-1;

        for($niv = 0; $niv <= $niveauMax; $niv++)
        {
            //on commence par crée les series pour chaque niveau
            if(!isset($series[$niv]))
            {
                $series[$niv]['name'] = $niv;
                $series[$niv]['size'] = ((100/($niveauMax+1))*($niv+1)).'%';
                $series[$niv]['innerSize'] = ((100/($niveauMax+1))*($niv)).'%';
                $series[$niv]['data'] = array();
            }
        }



        if($niveau <= $niveauMax)
        {

            $effectifDirect = $groupeRepo->findNumberOfMembreAtDate($groupeParent->getId());

            if($color == null)
                $color = 'rgba('.mt_rand(0,255).','.mt_rand(0,255).','.mt_rand(0,255).', 1)';

            for($niv = $niveau; $niv <= $niveauMax; $niv++) {

                //effectif direct du groupe.
                $data = array();
                $data['name'] = 'Eff. direct '.$groupeParent->getNom().' ('.$effectifDirect.' pers.)';
                $data['y'] = (($effectifDirect / $nbTotalMembre) * 100);
                $data['color'] = $color;
                array_push($series[$niv]['data'], $data);
            }


            $color = 'rgba('.mt_rand(0,255).','.mt_rand(0,255).','.mt_rand(0,255).', 1)';


            foreach($groupeParent->getEnfants() as $enfant) {
                $effectif = $groupeRepo->findNumberOfMembreAtDateRecursive($enfant->getId());
                $data = array();
                $data['name'] = $enfant->getNom().' ('.$effectif.' pers.)';
                $data['y'] = (($effectif / $nbTotalMembre) * 100);
                $data['color'] = $color;
                array_push($series[$niveau]['data'], $data);
            }

            foreach($groupeParent->getEnfants() as $enfant) {
                $series = $this->getGroupeData($groupeRepo,$enfant,$nbTotalMembre,$niveau+1,$niveauMax+1,$series,$color);
            }
        }

        return $series;




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
}
