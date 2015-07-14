<?php

namespace Interne\FinancesBundle\Controller;

use Interne\FinancesBundle\Entity\CreanceToMembre;
use Interne\FinancesBundle\Entity\CreanceToFamille;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;

use Interne\FinancesBundle\Form\CreanceAddType;
use Interne\FinancesBundle\Entity\Creance;
use Interne\FinancesBundle\SearchClass\CreanceSearch;
use Interne\FinancesBundle\Form\CreanceSearchType;
use Interne\FinancesBundle\SearchRepository\CreanceToMembreRepository;
use Interne\FinancesBundle\SearchRepository\CreanceToFamilleRepository;
use AppBundle\Entity\Membre;
use Interne\FinancesBundle\Entity\Facture;
use AppBundle\Utils\Listing\Liste;
use AppBundle\Utils\Listing\Lister;
use AppBundle\Entity\Groupe;

/* routing */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


/**
 * Class CreanceController
 * @package Interne\FinancesBundle\Controller
 * @Route("/creance")
 */
class CreanceController extends Controller
{
    /**
     *
     * Supprime une cérance.
     * Ne supprime que les cérances qui sont pas
     * liée a une facture.
     *
     *
     * @Route("/delete/{creance}", name="interne_finances_creance_delete", options={"expose"=true})
     * @param Creance $creance
     * @ParamConverter("creance", class="InterneFinancesBundle:Creance")
     * @param Request $request
     * @return Response
     */
    public function deleteAjaxAction(Request $request,Creance $creance)
    {
        /*
         * On vérifie que la cérance n'est pas liée à une facture avant de la supprimer
         */
        if(!$creance->isFactured()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($creance);
            $em->flush();

            $response = new Response();
            return $response->setStatusCode(200);//OK
        }
        $response = new Response();
        return $response->setStatusCode(409);//Conflict
    }


    /**
     * @Route("/show/{creance}", name="interne_finances_creance_show", options={"expose"=true})
     * @param Creance $creance
     * @ParamConverter("creance", class="InterneFinancesBundle:Creance")
     * @param Request $request
     * @Template("InterneFinancesBundle:Creance:modalContentShow.html.twig")
     * @return Response
     */
    public function showAjaxAction(Request $request,Creance $creance){

        return array('creance' => $creance);

    }

    /*
     * Ajoute des cérances en masse à la liste de membre (listing)
     * todo fixe me
     */
    /**
     * @param Request $request
     * @return Response
     */
    public function addCreanceToListingAjaxAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {

            $creance = new Creance();
            $creanceAddForm  = $this->createForm(new CreanceAddType,$creance);
            $creanceAddForm->submit($request);
            $creance = $creanceAddForm->getData();

            /*
             * On récupère les proporiété de la créance du formulaire
             */
            $titre = $creance->getTitre();
            $remarque = $creance->getRemarque();
            $montant = $creance->getMontantEmis();

            /*
             * On récupère la liste des ids de membre à qui ajouter une créance
             */
            $listeIds = $creanceAddForm->get('idsMembre');


            /*
             * On ajoute une cérance à chaque membre de la liste
             */
            foreach ($listeIds as $idMembre) {
                $creance = new Creance();
                $creance->setDateCreation(new \DateTime());
                $creance->setMontantEmis($montant);
                $creance->setRemarque($remarque);
                $creance->setTitre($titre);

                $em = $this->getDoctrine()->getManager();
                $membre = $em->getRepository('InterneFichierBundle:Membre')->find($idMembre);

                $membre->addCreance($creance);

                $em->persist($creance);
                $em->flush();
            }

            return new Response();

        }
        return new Response();
    }

    /*
     * Ajoute une cérance à un membre ou une famille
     */
    /**
     * @Route("/add_ajax", name="interne_finances_creance_add_ajax", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function addAjaxAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {

            $creance = new Creance();
            $creance->setDateCreation(new \DateTime());

            $creanceAddForm  = $this->createForm(new CreanceAddType,$creance);

            $creanceAddForm->submit($request);

            $creance = $creanceAddForm->getData();

            $classOwner = $creanceAddForm->get('classOwner')->getData();
            $idOwner = $creanceAddForm->get('idOwner')->getData();


            $em = $this->getDoctrine()->getManager();
            $creanceToAdd = null;
            if ($classOwner == 'Membre') {
                $membre = $em->getRepository('AppBundle:Membre')->find($idOwner);

                $creanceToAdd = new CreanceToMembre();
                $creanceToAdd->loadFromCreance($creance);

                $membre->addCreance($creanceToAdd);

            }
            elseif ($classOwner == 'Famille') {
                $famille = $em->getRepository('AppBundle:Famille')->find($idOwner);

                $creanceToAdd = new CreanceToFamille();
                $creanceToAdd->loadFromCreance($creance);

                $famille->addCreance($creanceToAdd);

            }

            $em->persist($creanceToAdd);
            $em->flush();

            return new Response('success');;

        }
        return new Response('error');
    }

    /*
     * Ajoute une cérance à un membre ou une famille
     */
    /**
     * @Route("/get_form_ajax", name="interne_finances_creance_get_form_ajax", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function sendFormAjaxAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {

            $ownerId = $request->request->get('ownerId');
            $ownerType = $request->request->get('ownerType');

            $creance = new Creance();
            $creanceAddForm  = $this->createForm(new CreanceAddType,$creance);
            $creanceAddForm->get('classOwner')->setData($ownerType);
            $creanceAddForm->get('idOwner')->setData($ownerId);


            return $this->render('InterneFinancesBundle:Creance:modalFormCreance.html.twig',
                array('creanceForm' => $creanceAddForm->createView()));


        }
        return new Response();


    }


    /**
     * @Route("/ajout_en_masse", name="interne_finances_creance_ajout_en_masse", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function pageAjoutCreanceEnMasseAction(Request $request)
    {

        /** @var Lister $listing */
        $listing = $this->get('listing');
        $arrayOfListe = $listing->getListes();

        $choices = array();

        /** @var Liste $liste */
        foreach($arrayOfListe as $liste)
        {
            $choices[$liste->getToken()] = $liste->name;
        }

        $ajoutForm = $this->createFormBuilder()
            ->add('creance',new CreanceAddType())
            ->add('groupes', 'entity', array(
                'class'		=> 'AppBundle:Groupe',
                'property'	=> 'nom',
                'multiple'=>true,
                'expanded'=>false,
                'required'=>false,
            ))
            ->add('listes','choice',array(
                'choices'=>$choices,
                'multiple'=>true,
                'expanded'=>false,
                'required'=>false,
            ))
            ->getForm();


        $ajoutForm->handleRequest($request);

        if ($ajoutForm->isValid()) {


            $em = $this->getDoctrine()->getManager();

            $groupes = $ajoutForm->get('groupes')->getData();
            $listes = $ajoutForm->get('listes')->getData();
            /** @var Creance $creance */
            $creance = $ajoutForm->get('creance')->getData();
            /** @var Groupe $groupe */
            foreach($groupes as $groupe)
            {
                foreach($groupe->getMembersRecursive() as $membre)
                {
                    $creanceToMembre = new CreanceToMembre();
                    $creanceToMembre->loadFromCreance($creance);
                    $creanceToMembre->setDateCreation(new \DateTime());
                    $creanceToMembre->setMembre($membre);

                    $em->persist($creanceToMembre);


                }
            }

            foreach($listes as $token)
            {
                $liste = $listing->getByToken($token);
                foreach($liste->getAll() as $membre)
                {
                    $creanceToMembre = new CreanceToMembre();
                    $creanceToMembre->loadFromCreance($creance);
                    $creanceToMembre->setDateCreation(new \DateTime());
                    $creanceToMembre->setMembre($membre);

                    $em->persist($creanceToMembre);
                }
            }
            $em->flush();

        }

        return $this->render('InterneFinancesBundle:Creance:page_ajout_creance_en_masse.html.twig',
            array('ajoutForm'=>$ajoutForm->createView()));
    }

    /**
     * @Route("/search", name="interne_finances_creance_search", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function searchAction(Request $request){

        $creanceSearch = new CreanceSearch();

        $searchForm = $this->createForm(new CreanceSearchType,$creanceSearch);

        $results = array();

        $searchForm->handleRequest($request);

        if ($searchForm->isValid()) {

            $creanceSearch = $searchForm->getData();

            $elasticaManager = $this->container->get('fos_elastica.manager');

            /** @var CreanceToMembreRepository $repository */
            $repository = $elasticaManager->getRepository('InterneFinancesBundle:CreanceToMembre');

            $resultsCreanceToMembre = $repository->search($creanceSearch);

            /** @var CreanceToFamilleRepository $repository */
            $repository = $elasticaManager->getRepository('InterneFinancesBundle:CreanceToFamille');

            $resultsCreanceToFamille = $repository->search($creanceSearch);

            $results = array_merge($resultsCreanceToMembre,$resultsCreanceToFamille);

        }

        return $this->render('InterneFinancesBundle:Creance:page_recherche.html.twig',
            array('searchForm'=>$searchForm->createView(),'creances'=>$results));
    }







}