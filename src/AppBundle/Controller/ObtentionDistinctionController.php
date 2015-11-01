<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Membre;
use AppBundle\Entity\ObtentionDistinction;
use AppBundle\Form\ObtentionDistinctionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ObtentionDistinctionController
 * @package AppBundle\Controller
 *
 * @Route("/obtention-distinction")
 */
class ObtentionDistinctionController extends Controller
{

    /**
     * @Route("/modal/add/{membre}", name="obtentiondistinction_add_modal", options={"expose"=true})
     *
     * @param Request $request
     * @param Membre $membre
     * @ParamConverter("membre", class="AppBundle:Membre")
     * @return Response
     */
    public function getAddModalAction(Request $request, Membre $membre)
    {
        $obtention = new ObtentionDistinction();

        $obtention->setMembre($membre);

        $obtentionForm = $this->createForm(new ObtentionDistinctionType(), $obtention, array(
            'action' => $this->generateUrl('obtention-distinction_add')
        ));

        return $this->render('AppBundle:ObtentionDistinction:obtention-distinctions_form_modal.html.twig', array(
                'form' => $obtentionForm->createView())
        );
    }


    /**
     * @Route("/modal/add/{membre}", name="obtention-distinction_add_membre_modal", options={"expose"=true})
     *
     * @param Request $request
     * @param Membre $membre
     * @ParamConverter("membre", class="AppBundle:Membre")
     * @return Response
     */
    public function getAddWithMemberModalAction(Request $request, Membre $membre)
    {
        $obtention = new ObtentionDistinction();

        /* Formulaire simple */
        $obtention->setMembre($membre);

        $obtentionForm = $this->createForm(new ObtentionDistinctionType(), $obtention, array(
            'action' => $this->generateUrl('obtention-distinction_add')
        ));

        return $this->render('AppBundle:ObtentionDistinction:obtention-distinctions_form_modal.html.twig', array(
                'form' => $obtentionForm->createView(),
                'postform' => $obtentionForm)
        );
    }

    /**
     * @Route("/modal/add/{obtention}", name="obtention-distinction_edit_modal", options={"expose"=true})
     *
     * @param Request $request
     * @param ObtentionDistinction $obtention
     * @ParamConverter("obtention", class="AppBundle:ObtentionDistinction")
     * @return Response
     */
    public function getEditModalAction(Request $request, ObtentionDistinction $obtention)
    {

        $obtentionForm = $this->createForm(
            new ObtentionDistinctionType(),
            $obtention,
            array('action' => $this->generateUrl('obtention-distinction_edit', array('obtention' => $obtention)))
        );


        return $this->render('AppBundle:ObtentionDistinction:obtention-distinctions_form_modal.html.twig', array(
                'form' => $obtentionForm->createView())
        );
    }


    /**
     * @Route("/add", name="obtention-distinction_add", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     */
    public function addObtentionDistinctionAction(Request $request)
    {
        $newObtention = new ObtentionDistinction();
        $newObtentionForm = $this->createForm(new ObtentionDistinctionType(), $newObtention);

        $newObtentionForm->handleRequest($request);

        if($newObtentionForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newObtention);
            $em->flush();

            return new JsonResponse(true);
        }

        return $this->render('AppBundle:ObtentionDistinction:obtention-distinctions_form_modal.html.twig', array(
            'form' => $newObtentionForm->createView(),
            'postform' => $newObtentionForm));
    }

    /**
     * @Route("/edit/{obtention-distinction}", name="obtention-distinction_edit", options={"expose"=true})
     *
     * @param Request $request
     * @param ObtentionDistinction $obtention
     * @return Response
     * @ParamConverter("obtention", class="AppBundle:ObtentionDistinction")
     */
    public function editObtentionDistinction(ObtentionDistinction $obtention, Request $request)
    {
        //TODO: modifier une obtention (ou peut-être ne veut-on que les supprimer ?)
    }

    /**
     * Supprime une obtention-distinction
     * @Route("/remove/{obtention-distinction}", name="obtention-distinction_delete", options={"expose"=true})
     * @ParamConverter("obtention-distinction", class="AppBundle:ObtentionDistinction")
     * @param $obtentionDistinction
     * @return JsonResponse
     */
    public function removeAttributionAction(ObtentionDistinction $obtentionDistinction)
    {

        $em = $this->getDoctrine()->getManager();

        $em->remove($obtentionDistinction);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

?>