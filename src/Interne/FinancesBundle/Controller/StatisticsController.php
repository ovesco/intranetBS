<?php

namespace Interne\FinancesBundle\Controller;

use Interne\FinancesBundle\Entity\Facture;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Interne\FinancesBundle\Entity\FactureRepository;

class StatisticsController extends Controller
{
    /**
     * @Route("/statistics", name="interne_fiances_statistics")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {

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


        $em = $this->getDoctrine()->getManager();
        $factures = $em->getRepository('InterneFinancesBundle:Facture')->findBySearch($facture);



        $maxNombreRappel = -1;
        $response = array();

        foreach($factures as $facture)
        {
            $nombreRappel = $facture->getNombreRappels();

            if($nombreRappel>$maxNombreRappel)
            {
                for($i = $maxNombreRappel+1; $i <= $nombreRappel; $i++)
                {
                    $response[$i] = 0;
                }

                $maxNombreRappel = $nombreRappel;
            }
            $response[$nombreRappel]++;
        }


        return $this->render('InterneFinancesBundle:Statistics:panel.html.twig',array('graph1Responses' => $response));
    }
}