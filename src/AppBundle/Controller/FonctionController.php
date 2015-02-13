<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GroupeModel;
use AppBundle\Form\GroupeModelType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\FonctionType;
use AppBundle\Entity\Fonction;

/**
 * Class StructureController
 * @package AppBundle\Controller
 *
 * @Route("/fonctions")
 */
class FonctionController extends Controller
{

    /**
     * @Route("/get_form_modale", name="fonction_get_form_modale", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     */
    public function getFonctionFormAjaxAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {

            $em = $this->getDoctrine()->getManager();
            /*
             * On envoie le formulaire en modal
             */
            $id = $request->request->get('idFonction');

            $fonction = null;
            $fonctionForm = null;
            if ($id == null) {
                /*
                 * Ajout
                 */
                $fonction = new Fonction();
                $fonctionForm = $this->createForm(new FonctionType(), $fonction,
                    array('action' => $this->generateUrl('fonction_add')));

            } else {
                /*
                 * Modification
                 */
                $fonction = $em->getRepository('AppBundle:Fonction')->find($id);
                $fonctionForm = $this->createForm(new FonctionType(), $fonction,
                    array('action' => $this->generateUrl('fonction_edit',array('fonction'=>$id))));

            }

            return $this->render('AppBundle:Fonction:fonction_modale_form.html.twig', array('form' => $fonctionForm->createView()));

        }
    }

    /**
     * @Route("/add", name="fonction_add", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     */
    public function addFonctionAction(Request $request)
    {
        $newFonction = new Fonction();
        $newFonctionForm = $this->createForm(new FonctionType(),$newFonction);

        $newFonctionForm->handleRequest($request);

        if($newFonctionForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newFonction);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('structure_gestion_fonctions'));
    }

    /**
     * @Route("/edit/{fonction}", name="fonction_edit", options={"expose"=true})
     *
     * @param Request $request
     * @param Fonction $fonction
     * @return Response
     * @ParamConverter("fonction", class="AppBundle:Fonction")
     */
    public function editFonctionction(Fonction $fonction,Request $request)
    {

        //$editedGroupe = new Groupe();
        $editedForm = $this->createForm(new FonctionType(),$fonction);

        $editedForm->handleRequest($request);

        if($editedForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

        }

        return $this->redirect($this->generateUrl('structure_gestion_fonctions'));
    }

}