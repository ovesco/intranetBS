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
use AppBundle\Utils\Menu\Menu;
use AppBundle\Utils\Response\ResponseFactory;
use AppBundle\Repository\CategorieRepository;

/* Entity */
use AppBundle\Entity\Categorie;

/* Form */
use AppBundle\Form\Categorie\CategorieType;

/**
 * Class CategorieController
 * @package AppBundle\Controller
 *
 * @Route("/intranet/structure/categorie")
 */
class CategorieController extends Controller
{

    /**
     * Page qui affiche les categorie de groupes
     *
     * @Route("/gestion", options={"expose"=true})
     * @param Request $request
     * @return Response
     * @Menu("Gestion des catégories", block="structure", order=4, icon="list")
     * @Template("AppBundle:Categorie:page_gestion.html.twig")
     */
    public function gestionAction(Request $request) {
        return array();
    }

    /**
     * @Route("/add", options={"expose"=true})
     * @Template("AppBundle:Categorie:modal_form.html.twig")
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_CATEGORIE_CREATE');

        $new = new Categorie();
        $newForm = $this->createForm(new CategorieType(),$new,
            array('action' => $this->generateUrl('app_categorie_add')));

        $newForm->handleRequest($request);

        if($newForm->isValid())
        {
            $this->get('app.repository.categorie')->save($new);
            return ResponseFactory::ok();
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
        $this->denyAccessUnlessGranted('edit',$categorie);

        $editedForm = $this->createForm(new CategorieType(),$categorie,
            array('action' => $this->generateUrl('app_categorie_edit',array('categorie'=>$categorie->getId()))));

        $editedForm->handleRequest($request);

        if($editedForm->isValid())
        {
            $this->get('app.repository.categorie')->save($categorie);
            return ResponseFactory::ok();
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
        $this->denyAccessUnlessGranted('remove',$categorie);

        if($categorie->isRemovable())
        {
            $this->get('app.repository.categorie')->remove($categorie);
            return ResponseFactory::ok();
        }
        else
        {
            return ResponseFactory::forbidden();
        }
    }





}