<?php

namespace AppBundle\Controller;

/* Symfony */
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;

/* Entity */
use AppBundle\Entity\Categorie;

/* Form */
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
     * Page qui affiche les categorie de groupes
     *
     * @Route("/liste", options={"expose"=true})
     * @param Request $request
     * @return Response
     *
     */
    public function listeAction(Request $request) {

        $em = $this->getDoctrine()->getManager();

        //retourne toutes les fonctions
        $categories = $em->getRepository('AppBundle:Categorie')->findAll();

        return $this->render('AppBundle:Categorie:page_liste.html.twig',array(
            'categories' =>$categories));


    }

    /**
     * @Route("/new", options={"expose"=true})
     * @Template("AppBundle:Categorie:modal_form.html.twig")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $new = new Categorie();
        $newForm = $this->createForm(new CategorieType(),$new,
            array('action' => $this->generateUrl('app_categorie_new')));

        $newForm->handleRequest($request);

        if($newForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($new);
            $em->flush();
            return $this->redirect($this->generateUrl('app_categorie_liste'));
        }

        return array('form'=>$newForm->createView());
    }


    /**
     * @Route("/edit/{categorie}", options={"expose"=true})
     * @param Request $request
     * @param Categorie $categorie
     * @return Response
     * @ParamConverter("categorie", class="AppBundle:Categorie")
     * @Template("AppBundle:Categorie:modal_form.html.twig")
     */
    public function editAction(Categorie $categorie,Request $request)
    {
        $editedForm = $this->createForm(new CategorieType(),$categorie,
            array('action' => $this->generateUrl('app_categorie_edit',array('categorie'=>$categorie->getId()))));

        $editedForm->handleRequest($request);
        if($editedForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirect($this->generateUrl('app_categorie_liste'));
        }
        return array('form'=>$editedForm->createView());
    }

    /**
     * @Route("/remove/{categorie}", options={"expose"=true})
     * @param Request $request
     * @param Categorie $categorie
     * @return Response
     * @ParamConverter("categorie", class="AppBundle:Categorie")
     */
    public function removeAction(Categorie $categorie,Request $request)
    {
        if($categorie->isRemovable())
        {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $em->remove($categorie);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'info',
                'Categorie supprimée'
            );
        }
        else
        {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Impossible de supprimer cette categorie'
            );
        }
        return $this->redirect($this->generateUrl('app_categorie_liste'));
    }





}