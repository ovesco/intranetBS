<?php

namespace Interne\FactureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Interne\FactureBundle\Entity\Rappel;
use Interne\FactureBundle\Entity\Facture;
use Interne\FactureBundle\Entity\Creance;
use Interne\FactureBundle\Form\FactureSearchType;
use Interne\FactureBundle\Form\CreanceSearchType;
use Interne\FactureBundle\Entity\CreanceRepository;

class SearchController extends Controller
{


    public function searchAction()
    {
        $facture = new Facture();
        $creance = new Creance();

        //on supprime les valeurs existante à cause du constructeur
        $creance->setMontantEmis(null);
        $creance->setMontantRecu(null);

        /*
         * On crée les formulaires
         */
        $factureSearchForm  = $this->createForm(new FactureSearchType, $facture);
        $creanceSearchForm  = $this->createForm(new CreanceSearchType, $creance);


        /*
         * On récupère la session qui contient la liste des factures/creances des
         * recherches précédentes
         */
        $session = $this->getRequest()->getSession();



        $request = $this->getRequest();

        if($request->isXmlHttpRequest()) {

            $facture = null;
            $creance = null;


            if ($request->request->has('InterneFactureBundleFactureSearchType')) {

                $factureSearchForm->submit($request);
                $facture = $factureSearchForm->getData();

            }
            if ($request->request->has('InterneFactureBundleCreanceSearchType')) {

                $creanceSearchForm->submit($request);
                $creance = $creanceSearchForm->getData();

            }


            /*
             * ce lien est utile pour la recherche sur les facture
             */
            $facture->addCreance($creance);

            /*
             * On récupère les éléments de recherche non compris dans la facture.
             * Tableau contenant les paramètres de recherche suplémentaire
             */
            $searchParameters = array(
                'facture' => array(

                    'nombreRappel' => $factureSearchForm->get('nombreRappel')->getData(),

                    'montantRecuMaximum' => $factureSearchForm->get('montantRecuMaximum')->getData(),
                    'montantRecuMinimum' => $factureSearchForm->get('montantRecuMinimum')->getData(),

                    'montantTotal' => $factureSearchForm->get('montantTotal')->getData(),

                    'datePayementMaximum' => $factureSearchForm->get('datePayementMaximum')->getData(),
                    'datePayementMinimum' => $factureSearchForm->get('datePayementMinimum')->getData(),
                ),
                'creance' => array(


                    'montantEmisMaximum' => $creanceSearchForm->get('montantEmisMaximum')->getData(),
                    'montantEmisMinimum' => $creanceSearchForm->get('montantEmisMinimum')->getData(),
                    'montantRecuMaximum' => $creanceSearchForm->get('montantRecuMaximum')->getData(),
                    'montantRecuMinimum' => $creanceSearchForm->get('montantRecuMinimum')->getData(),

                    'dateCreationMaximum' => $creanceSearchForm->get('dateCreationMaximum')->getData(),
                    'dateCreationMinimum' => $creanceSearchForm->get('dateCreationMinimum')->getData(),

                    'membreNom' => $creanceSearchForm->get('membreNom')->getData(),
                    'membrePrenom' => $creanceSearchForm->get('membrePrenom')->getData(),

                    'familleNom' => $creanceSearchForm->get('familleNom')->getData(),

                    'isLinkedToFacture' => $creanceSearchForm->get('isLinkedToFacture')->getData(),
                    'searchOption' => $creanceSearchForm->get('searchOption')->getData(),
                )


            );

            /*
             * pour la recherche on utilise la fonction personalisée de
             * recheche de facture qui se trouve dans factureRepository.php
             */
            $em = $this->getDoctrine()->getManager();

            $factures = null;
            $creances = null;

            if($searchParameters['creance']['isLinkedToFacture'] == 'yes')
            {
                $factures = $em->getRepository('InterneFactureBundle:Facture')->findBySearch($facture,$searchParameters);
                foreach($factures as $facture)
                {
                    foreach($facture->getCreances() as $creance)
                    {
                        $creances[] = $creance;
                    }
                }
            }
            else
            {
                /*
                 * On fait une recherche spécifique aux cérances qui ne
                 * sont pas encore liée à des factures.
                 */
                $creances = $em->getRepository('InterneFactureBundle:Creance')->findBySearch($creance,$searchParameters);
            }


            /*
             * Gestion de la session...selon l'option de recherche, on ajoute supprime les factures/creances
             */

            $option = $searchParameters['creance']['searchOption'];

            if($option == 'new')
            {
                /*
                 * Nouvelle recherche, on met a jour les session avec les nouveaux resultats
                 */
                $session->set('factures',$factures);
                $session->set('creances',$creances);
            }
            if($option == 'add')
            {
                /*
                 * On vérifie que les creances/factures trouvée n'existe
                 * pas déjà dans la liste contenue en session.
                 */
                $facturesSession = $session->get('factures');
                foreach($factures as $facture)
                {
                    $found = false;
                    foreach($facturesSession as $factureSession)
                    {
                        if($facture->getId() == $factureSession->getId())
                            $found = true;
                    }
                    if(!$found)
                    {
                        array_push($facturesSession,$facture);
                    }
                }
                $session->set('factures',$facturesSession);

                $creancesSession = $session->get('creances');
                foreach($creances as $creance)
                {
                    $found = false;
                    foreach($creancesSession as $creanceSession)
                    {
                        if($creance->getId() == $creanceSession->getId())
                            $found = true;
                    }
                    if(!$found)
                    {
                        array_push($creancesSession,$creance);
                    }
                }
                $session->set('creances',$creancesSession);
            }
            if($option == 'substract')
            {
                /*
                 * On enleve les creances/factures trouvée de la liste
                 */
                $facturesSession = $session->get('factures');
                $newFactureSession = array();

                foreach($facturesSession as $factureSession)
                {
                    $found = false;
                    foreach($factures as $facture)
                    {
                        if($facture->getId() == $factureSession->getId())
                            $found = true;
                    }
                    if(!$found)
                    {
                        array_push($newFactureSession,$factureSession);
                    }
                }
                $session->set('factures',$newFactureSession);

                $creancesSession = $session->get('creances');
                $newCreancesSession = array();

                foreach($creancesSession as $creanceSession)
                {
                    $found = false;
                    foreach($creances as $creance)
                    {
                        if($creance->getId() == $creanceSession->getId())
                            $found = true;
                    }
                    if(!$found)
                    {
                        array_push($newCreancesSession,$creanceSession);
                    }
                }
                $session->set('creances',$newCreancesSession);
            }

            return $this->render('InterneFactureBundle:Search:results.html.twig', array(

                'factures' => $session->get('factures'),
                'creances' => $session->get('creances')
            ));

        }


        return $this->render('InterneFactureBundle:Search:search.html.twig', array(
            'formFacture' => $factureSearchForm->createView(),
            'formCreance' => $creanceSearchForm->createView(),
            'factures' => $session->get('factures'),
            'creances' => $session->get('creances')
        ));

    }




}