<?php

namespace Interne\FactureBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Request;
use Interne\FactureBundle\Entity\Facture;
use Interne\FactureBundle\Entity\Creance;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Null;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ValidationController extends Controller
{
    public function indexAction()
    {
        return $this->render('InterneFactureBundle:Validation:validation.html.twig');
    }

    public function uploadFileAjaxAction()
    {
        $request = $this->getRequest();

        if($request->isXmlHttpRequest())
        {
            $file = $request->files->get('file');

            $array = $this->extractFacturesInFile($file);

            $facturesInFile = $array['factures'];
            $infos = $array['infos'];

            /*
             * On va comparer les facture dans le fichier avec ce qui il y a dans la basse de donnée.
             */
            $results = $this->compareWithFactureInBDD($facturesInFile);

            return $this->render('InterneFactureBundle:Validation:tableLineInput.html.twig',
                array(
                    'results' => $results

                ));




        }


    }

    public function addManualyAjaxAction()
    {

        $request = $this->getRequest();

        if($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');
            $montantRecu = $request->request->get('montantRecu');


            $id = (int)$id; //cast sur int

            $receivedFactures = new ArrayCollection();

            $facture = new Facture();
            $facture->setId($id);
            $facture->setMontantRecu($montantRecu);


            $facture->setDatePayement(new \DateTime());

            $receivedFactures[] = $facture;

            /*
             * On va comparer les facture dans le fichier avec ce qui il y a dans la basse de donnée.
             */
            $results = $this->compareWithFactureInBDD($receivedFactures);



            return $this->render('InterneFactureBundle:Validation:tableLineInput.html.twig',
                array(
                    'results' => $results

                ));

        }

        return new Response();
    }

    public function validationAjaxAction()
    {
        $request = $this->getRequest();

        if($request->isXmlHttpRequest())
        {
            $id = $request->request->get('id');
            $montantRecu = $request->request->get('montantRecu');
            $datePayement = $request->request->get('datePayement');
            $state = $request->request->get('state');


            $id = (int)$id; //cast sur int

            $em = $this->getDoctrine()->getManager();
            $facture = $em->getRepository('InterneFactureBundle:Facture')->find($id);


            $facture->setMontantRecu($montantRecu);
            $facture->setStatut('payee');
            $date = new \DateTime();
            $date->createFromFormat('d/m/Y',$datePayement);
            $facture->setDatePayement($date);


            $creanceRepartition = [];
            if(($state == 'found_lower_valid')||($state == 'found_lower_new_facture'))
            {
                $creancesIdArray = $request->request->get('creancesIdArray');
                $creancesMontantArray = $request->request->get('creancesMontantArray');



                $i = 0;
                foreach($creancesIdArray as $idCreance)
                {
                    $creanceRepartition[$idCreance] = $creancesMontantArray[$i];
                    $i++;
                }

            }

            switch($state){
                case 'found_valid':
                    foreach($facture->getCreances() as $creance)
                    {
                        $creance->setMontantRecu($creance->getMontantEmis);
                    }
                    break;
                case 'found_lower_valid':
                    foreach($facture->getCreances() as $creance)
                    {
                        $creance->setMontantRecu($creanceRepartition[$creance->getId()]);
                    }
                    break;
                case 'found_lower_new_facture':
                    foreach($facture->getCreances() as $creance)
                    {
                        $creance->setMontantRecu($creanceRepartition[$creance->getId()]);
                    }
                    break;
            }


            if($state == 'found_lower_new_facture')
            {
                /*
                 * dans ce cas de figure, on crée des créances supplémentaires
                 * pour compenser le montant exigé
                 */

                foreach($facture->getCreances() as $creance)
                {
                    if(!$creance->isPayed())
                    {
                        $newCreance = new Creance();
                        $newCreance->setTitre($creance->getTitre());
                        $newCreance->setMembre($creance->getMembre());
                        $newCreance->setFamille($creance->getFamille());
                        /*
                         * calcule du montant restant
                         */
                        $solde = $creance->getMontantEmis() - $creanceRepartition[$creance->getId()];
                        $newCreance->setMontantEmis($solde);

                        $remarque = $creance->getRemarque()
                            .' (Crée en complément de la facture numéro: '
                            .$facture->getId()
                            .')';
                        $newCreance->setRemarque($remarque);
                        $em->persist($newCreance);

                        /*
                         * On ajoute aussi une remarque dans la créance qui vient d'être validée.
                         */
                        $remarque = $creance->getRemarque()
                            .' (Une Créance de complément à été crée)';
                        $creance->setRemarque($remarque);

                    }
                }
            }

            $em->flush();

            return new JsonResponse($facture->getId());

        }


        return new JsonResponse();


    }

    private function extractFacturesInFile($file)
    {
        /*
         * extraction du contenu du fichier.
         */
        $fileString = file($file);
        $nbLine = count($fileString);

        /*
         * création des conteneurs de résultats de la fonction.
         */
        $facturesInFile = new ArrayCollection();
        $infos = array();

        /*
         * analyse ligne par ligne du fichier-
         */
        for ($i = 0; $i < $nbLine; $i++) {

            $line = $fileString[$i];
            $infos = array();
            $infos['rejetsBvr'] = 0;

            if (substr($line, 0, 1) != 9) {
                //extraction des infos de la ligne
                $numRef = substr($line, 12, 26);
                $montantRecu = substr($line, 39, 10);
                $datePayement = substr($line, 71, 6);
                $rejetBVR = substr($line, 86, 1);

                /*
                 * enregistre le nombre de facture qui ont
                 * été rejetée et rentrée à la main par
                 * la poste.
                 */
                if($rejetBVR)
                {
                    $infos['rejetsBvr'] =$infos['rejetsBvr']+1;
                }

                //reformatage des chaines de caractère
                $numRef = (integer)ltrim($numRef,0);
                $montantRecu = (float)(ltrim($montantRecu,0)/100);
                $date_payement_annee = '20'. substr($datePayement,0,2);
                $date_payement_mois = substr($datePayement,2,2);
                $date_payement_jour = substr($datePayement,4,2);
                $datePayement = new \DateTime();
                $datePayement->setDate((int)$date_payement_annee,(int)$date_payement_mois,(int)$date_payement_jour);

                /*
                 * création de la facture extraite de la ligne
                 */
                $facture = new Facture();
                $facture->setId($numRef);
                $facture->setMontantRecu($montantRecu);
                $facture->setDatePayement($datePayement);

                $facturesInFile[] = $facture;
            }
            else
            {
                /*
                 * Infos sur les factures présente dans ce fichier.
                 * Elle sont stoquées sur la ligne qui commence
                 * par un 9.
                 */
                $infos['genreTransaction'] = substr($line, 0, 3);
                $infos['montantTotal'] = ltrim(substr($line, 39, 12),0);
                $infos['nbTransactions'] = ltrim(substr($line, 51, 12),0);
                $infos['dateDisquette'] = substr($line, 63, 6);
                $infos['taxes'] = substr($line, 69, 9);

            }
        }



        return array('factures' => $facturesInFile, 'infos' => $infos);
    }

    private function compareWithFactureInBDD($facturesReceived)
    {
        $em = $this->getDoctrine()->getManager();
        $factureRepository = $em->getRepository('InterneFactureBundle:Facture');


        $results = array();

        foreach($facturesReceived as $factureReceived)
        {


            $factureFound = $factureRepository->find($factureReceived->getId());

            if($factureFound != Null)
            {
                if($factureFound->getStatut() == 'ouverte')
                {
                    $montantTotalEmis = $factureFound->getMontantTotal();
                    $montantRecu = $factureReceived->getMontantRecu();

                    if($montantTotalEmis == $montantRecu)
                    {
                        $validationStatut = 'Found:Valid';
                    }
                    elseif($montantTotalEmis > $montantRecu)
                    {
                        $validationStatut = 'Found:Lower';
                    }
                    elseif($montantTotalEmis < $montantRecu)
                    {
                        $validationStatut = 'Found:Upper';
                    }
                }
                else
                {
                    /*
                     * la facture a déjà été payée
                     */
                    $validationStatut = 'Found:AlreadyPayed';
                }


            }
            else
            {
                $validationStatut = 'NotFound';
            }

            $results[] = array(
                'factureReceived' => $factureReceived,
                'factureFound' => $factureFound,
                'validationStatut' => $validationStatut
            );



        }



        return $results;
    }
}
