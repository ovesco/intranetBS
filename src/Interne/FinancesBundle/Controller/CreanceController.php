<?php

namespace Interne\FinancesBundle\Controller;

use Interne\FinancesBundle\Entity\CreanceToMembre;
use Interne\FinancesBundle\Entity\CreanceToFamille;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Interne\FinancesBundle\Form\CreanceAddType;
use Interne\FinancesBundle\Entity\Creance;
use AppBundle\Entity\Membre;
use Interne\FinancesBundle\Entity\Facture;




class CreanceController extends Controller
{
    /*
     * Supprime une cérance en ajax.
     * Ne supprime que les cérances qui sont pas encore
     * liée a une facture.
     */
    /**
     * @Route("/creance/delete_ajax", name="interne_fiances_creance_delete_ajax", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function deleteAjaxAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {

            $id = $request->request->get('idCreance');
            $em = $this->getDoctrine()->getManager();
            $creance = $em->getRepository('InterneFinancesBundle:Creance')->find($id);

            /*
             * On vérifie que la cérance n'est pas liée à une facture avant de la supprimer
             */
            if(!$creance->isFactured())
            {
                $em->remove($creance);
                $em->flush();
            }

            return new Response('success');
        }
        return new Response('error');
    }





    /**
     * @Route("/creance/show_ajax", name="interne_fiances_creance_show_ajax", options={"expose"=true})
     *
     * @return Response
     */
    public function showAjaxAction(){

        $request = $this->getRequest();

        if($request->isXmlHttpRequest()) {

            $id = $request->request->get('idCreance');
            $em = $this->getDoctrine()->getManager();
            $creance = $em->getRepository('InterneFinancesBundle:Creance')->find($id);
            return $this->render('InterneFinancesBundle:Creance:modalContentShow.html.twig',
                array('creance' => $creance));
        }
        return new Response();

    }

    /*
     * Ajoute des cérances en masse à la liste de membre (listing)
     *
     */
    /**
     * @return Response
     */
    public function addCreanceToListingAjaxAction()
    {
        $request = $this->getRequest();

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
     * @Route("/creance/add_ajax", name="interne_fiances_creance_add_ajax", options={"expose"=true})
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
     * @Route("/creance/get_form_ajax", name="interne_fiances_creance_get_form_ajax", options={"expose"=true})
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







}