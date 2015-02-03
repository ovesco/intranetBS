<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Categorie;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Form\CategorieType;

/**
 * Class CategorieController
 * @package AppBundle\Controller
 *
 * @Route("/categorie")
 */
class CategorieController extends Controller
{

    /**
     * @Route("/get_form_modale", name="categrorie_get_form_modale", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     */
    public function getCategorieFormAjaxAction(Request $request)
    {

        if ($request->isXmlHttpRequest()) {

            $em = $this->getDoctrine()->getManager();

            /*
             * On envoie le formulaire en modal
             */
            $id = $request->request->get('idCategorie');

            $categorie = null;
            $categorieForm = null;
            if($id == null)
            {
                /*
                 * Ajout
                 */
                $categorie = new Categorie();
                $categorieForm = $this->createForm(new CategorieType(),$categorie,
                    array('action' => $this->generateUrl('categorie_add')));

            }
            else
            {

                $categorie = $em->getRepository('AppBundle:Categorie')->find($id);
                $categorieForm = $this->createForm(new CategorieType(),$categorie,
                    array('action' => $this->generateUrl('categorie_edit',array('categorie'=>$id))));

            }

            return $this->render('AppBundle:Categorie:categorie_modale_form.html.twig',array('form'=>$categorieForm->createView()));

        }
        return new Response();
    }

    /**
     * @Route("/add", name="categorie_add", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     */
    public function addCategorieAction(Request $request)
    {
        $new = new Categorie();
        $newForm = $this->createForm(new CategorieType(),$new);

        $newForm->handleRequest($request);

        if($newForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($new);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('structure_gestion_categorie'));
    }

    /**
     * @Route("/edit/{categorie}", name="categorie_edit", options={"expose"=true})
     *
     * @param Request $request
     * @param Categorie $categorie
     * @return Response
     * @ParamConverter("categorie", class="AppBundle:Categorie")
     */
    public function editFonctionction(Categorie $categorie,Request $request)
    {

        $editedForm = $this->createForm(new CategorieType(),$categorie);

        $editedForm->handleRequest($request);

        if($editedForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

        }

        return $this->redirect($this->generateUrl('structure_gestion_categorie'));
    }


}