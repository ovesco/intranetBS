<?php

namespace Interne\FactureBundle\Controller;

use Interne\FactureBundle\Entity\Facture;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Collections\ArrayCollection;
use Interne\FactureBundle\Entity\FactureRepository;

class StatisticsController extends Controller
{
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
        $factures = $em->getRepository('InterneFactureBundle:Facture')->findBySearch($facture);



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


        return $this->render('InterneFactureBundle:Statistics:panel.html.twig',array('graph1Responses' => $response));
    }
}