<?php

namespace Interne\FinancesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Interne\FinancesBundle\Entity\Facture;
use Interne\FinancesBundle\Entity\FactureRepository;


class DefaultController extends Controller
{

    /**
     * @Route("/dev", name="interne_fiances_dev")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {


        $test = null;
        return $this->render('InterneFinancesBundle:Default:index.html.twig', array('test' => $test));
    }

    /**
     * @Route("/mode_emploi", name="interne_fiances_mode_emploi")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function modeEmploiAction()
    {
        return $this->render('InterneFinancesBundle:Default:modeEmploi.html.twig');
    }



}
