<?php

namespace Interne\FinancesBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Request;
use Interne\FinancesBundle\Entity\Facture;
use Interne\FinancesBundle\Entity\Creance;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Null;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Interne\FinancesBundle\Entity\Payement;

/**
 * Class ValidationController
 * @package Interne\FinancesBundle\Controller
 * @route("/validation")
 */
class ValidationController extends Controller
{
    /**
     * @route("/", name="interne_finances_validation")
     *
     * @return Response
     */
    public function indexAction()
    {
        /*
         * On récupère tout les payements en attente.
         */
        $em = $this->getDoctrine()->getManager();
        $payements = $em->getRepository('InterneFinancesBundle:Payement')->findByState('waiting');

        $results = $this->compareWithFactureInBDD($payements);

        return $this->render('InterneFinancesBundle:Validation:validation.html.twig', array('results'=>$results));
    }

    /**
     * @route("/process", name="interne_finances_validation_process", options={"expose"=true})
     * @return Response
     */
    public function validationAjaxAction()
    {
        $request = $this->getRequest();

        if($request->isXmlHttpRequest())
        {
            //on récupère les données du formulaire
            $idPayement = $request->request->get('idPayement');
            $state = $request->request->get('state');

            //conversion string to other type
            $idPayement = intval($idPayement); //cast sur int


            //chargement BDD
            $em = $this->getDoctrine()->getManager();
            $payementRepo = $em->getRepository('InterneFinancesBundle:Payement');
            $factureRepo = $em->getRepository('InterneFinancesBundle:Facture');

            //chargement du payement
            $payement = $payementRepo->find($idPayement);

            //nouvel etat du payement
            $payement->setState($state);

            //récupération des infos du payement
            $idFacture = $payement->getIdFacture();
            $montantRecu = $payement->getMontantRecu();
            $datePayement = $payement->getDatePayement();


            //traitement du payement
            if($state != 'not_found')
            {

                $facture = $factureRepo->find($idFacture);

                //on met a jour les infos de la facture
                $facture->setStatut('payee');
                $facture->setDatePayement($datePayement);


                //On récupère la répartition des montants.
                $creanceRepartition = [];
                $rappelRepartition = [];
                if(($state == 'found_lower_valid')||($state == 'found_lower_new_facture')||($state == 'found_upper'))
                {
                    $creancesIdArray = $request->request->get('creancesIdArray');
                    $creancesMontantArray = $request->request->get('creancesMontantArray');

                    //on reformate l'information contenue dans les tableaux
                    $i = 0;
                    foreach($creancesIdArray as $idCreance)
                    {
                        $creanceRepartition[$idCreance] = $creancesMontantArray[$i];
                        $i++;
                    }

                    $rappelsIdArray = $request->request->get('rappelsIdArray');
                    $rappelsMontantArray = $request->request->get('rappelsMontantArray');

                    //on reformate l'information contenue dans les tableaux
                    if($rappelsIdArray != null)
                    {
                        $i = 0;
                        foreach($rappelsIdArray as $idRappel)
                        {
                            $rappelRepartition[$idRappel] = $rappelsMontantArray[$i];
                            $i++;
                        }
                    }


                }

                switch($state){
                    case 'found_valid':

                        //validation des créances de la factures
                        foreach($facture->getCreances() as $creance)
                        {
                            $creance->setMontantRecu($creance->getMontantEmis);
                        }

                        //validationd des rappels de la facture
                        foreach($facture->getRappels() as $rappel)
                        {
                            $rappel->setMontantRecu($rappel->getFrais());
                        }
                        break;

                    case 'found_upper':
                        foreach($facture->getCreances() as $creance)
                        {
                            $creance->setMontantRecu($creanceRepartition[$creance->getId()]);
                        }
                        foreach($facture->getRappels() as $rappel)
                        {
                            $rappel->setMontantRecu($rappelRepartition[$rappel->getId()]);
                        }
                        break;
                    case 'found_lower_valid':
                        foreach($facture->getCreances() as $creance)
                        {
                            $creance->setMontantRecu($creanceRepartition[$creance->getId()]);
                        }
                        foreach($facture->getRappels() as $rappel)
                        {
                            $rappel->setMontantRecu($rappelRepartition[$rappel->getId()]);
                        }
                        break;
                    case 'found_lower_new_facture':
                        foreach($facture->getCreances() as $creance)
                        {

                            $creance->setMontantRecu($creanceRepartition[$creance->getId()]);

                            /*
                             * dans ce cas de figure, on crée des créances supplémentaires
                             * pour compenser le montant exigé
                             */
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

                        /*
                         * On crée une créance de compensation des eventuelles frais de rappel
                         * non payé.
                         */
                        $montantNonPaye = 0;
                        foreach($facture->getRappels() as $rappel)
                        {
                            $montantRecu = $rappelRepartition[$rappel->getId()];
                            $montantEmis = $rappel->getMontantEmis();
                            $rappel->setMontantRecu($montantRecu);
                            $rappel->setDatePayement($datePayement);
                            $montantNonPaye = $montantNonPaye + ($montantEmis-$montantRecu);
                        }
                        if($montantNonPaye > 0)
                        {
                            $newCreance = new Creance();
                            $newCreance->setTitre('Complément pour frais de rappel');
                            $newCreance->setMembre($creance->getMembre());
                            $newCreance->setFamille($creance->getFamille());
                            $newCreance->setDateCreation(new \DateTime());

                            $newCreance->setMontantEmis($montantNonPaye);

                            $remarque = $creance->getRemarque()
                                .'Crée en complément de la facture numéro: '
                                .$facture->getId()
                                .' en raison de frais de rappel non versés';
                            $newCreance->setRemarque($remarque);
                            $em->persist($newCreance);

                        }



                        break;
                }
            }
            $em->flush();

            return new Response();

        }


        return new Response();


    }

    private function compareWithFactureInBDD($payements)
    {
        $em = $this->getDoctrine()->getManager();
        $factureRepository = $em->getRepository('InterneFinancesBundle:Facture');


        $results = array();

        foreach($payements as $payement)
        {


            $factureFound = $factureRepository->find($payement->getIdFacture());

            if($factureFound != Null)
            {
                if($factureFound->getStatut() == 'ouverte')
                {
                    $montantTotalEmis = $factureFound->getMontantEmis();
                    $montantRecu = $payement->getMontantRecu();

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
                'payement' => $payement,
                'factureFound' => $factureFound,
                'validationStatut' => $validationStatut
            );



        }



        return $results;
    }
}
