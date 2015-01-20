<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        return $this->render('AppBundle:statistics:panel.html.twig');
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
            case 'effectifs_pie_charts':
                return $this->getEffectifsPieCharts();
            default:
                return $this->getEffectifsPieCharts();
        }

    }

    private function getGroupeData(Groupe $groupeParent, $nbTotalMembre,$niveau,$niveauMax,$series,$color = null)
    {
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


        //couleur rand
        if($niveau <= $niveauMax)
        {

            if($color == null)
                $color = 'rgba('.mt_rand(0,255).','.mt_rand(0,255).','.mt_rand(0,255).', 1)';

            for($niv = $niveau; $niv <= $niveauMax; $niv++) {

                //effectif direct du groupe.
                $data = array();
                $data['name'] = 'Eff. direct '.$groupeParent->getNom();
                $data['y'] = ((count($groupeParent->getMembers()) / $nbTotalMembre) * 100);
                $data['color'] = $color;
                array_push($series[$niv]['data'], $data);
            }


            $color = 'rgba('.mt_rand(0,255).','.mt_rand(0,255).','.mt_rand(0,255).', 1)';

            /** @var Groupe $enfant */
            foreach($groupeParent->getEnfants() as $enfant) {
                $data = array();
                $data['name'] = $enfant->getNom();
                $data['y'] = ((count($enfant->getMembersRecursive()) / $nbTotalMembre) * 100);
                $data['color'] = $color;
                array_push($series[$niveau]['data'], $data);
            }



            foreach($groupeParent->getEnfants() as $enfant) {
                $series = $this->getGroupeData($enfant,$nbTotalMembre,$niveau+1,$niveauMax,$series,$color);
            }

        }

        return $series;




    }

    private function getEffectifsPieCharts(){

        $groupeRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:Groupe');

        $parentId = null;
        $groupeParent = new Groupe();
        if($parentId == null)
        {
            $groupes =$groupeRepo->findBy(array('parent'=>$parentId));
            $groupeParent->setEnfants($groupes);

        }
        else
        {
            $groupeParent = $groupeRepo->find($parentId);
        }

        $nombreTotalMembre = count($groupeParent->getMembersRecursive());

        $series = array();
        $data = $this->getGroupeData($groupeParent,$nombreTotalMembre,0,3,$series);



        $graphData = array(

            'chart' => array(
                'type' => 'pie'
            ),
            'title' => array(
                'text'=> 'Pie'
            ),
            'xAxis' => array(
                'text'=>'test',

            ),
            'yAxis' => array(
                'text' => 'test2',
            ),

            'tooltip' => array(
                'valueSufix' => '%',
            ),
            'plotOptions' => array(
                'pie' => array(
                    'shadow' => false,
                    'center' => array('50%','50%')
                )
            ),


            'series' =>   $data

        );

        return $graphData;
    }
}
