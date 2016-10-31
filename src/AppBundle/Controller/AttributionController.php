<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Attribution;
use AppBundle\Entity\Membre;
use AppBundle\Form\Attribution\AttributionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AttributionController
 * @package AppBundle\Controller
 *
 * @Route("/intranet/attribution")
 */
class AttributionController extends Controller
{

    /**
     * @Route("/add", name="app_attribution_add", options={"expose"=true})
     * @Route("/add/{membre}", name="app_attribution_add_tomembre", options={"expose"=true})
     *
     * @param Request $request
     * @param Membre $membre
     * @ParamConverter("membre", class="AppBundle:Membre")
     * @return Response
     */
    public function addAction(Request $request, Membre $membre = null)
    {
        $attribution = new Attribution();

        if ($membre != null)
            $attribution->setMembre($membre);

        $em = $this->getDoctrine()->getManager();

        $attributionForm = $this->createForm(new AttributionType($em), $attribution);
        $attributionForm->handleRequest($request);


        if ($attributionForm->isSubmitted() && $attributionForm->isValid()) {
            $em->persist($attribution);
            $em->flush();

            return new JsonResponse($attribution, Response::HTTP_CREATED);
        }

        $attributionForm = $this->createForm(new AttributionType($em), $attribution, array(
            'action' => $this->generateUrl('app_attribution_add')
        ));

        return $this->render('AppBundle:Attribution:attribution_form_modal.html.twig', array(
            'form' => $attributionForm->createView())
        );
    }


    /**
     * @Route("/edit/{attribution}", name="app_attribution_edit", options={"expose"=true})
     * @Route("/end/{attribution}/{dateFin}", name="app_attribution_end", options={"expose"=true})
     *
     * @param Request $request
     * @param Attribution $attribution
     * @param \DateTime $dateFin
     * @ParamConverter("attribution", class="AppBundle:Attribution")
     * @ParamConverter("dateFin", class="\DateTime")
     * @return Response
     */
    public function editAction(Request $request, Attribution $attribution, \DateTime $dateFin = null)
    {
        $attribution->setDateFin($dateFin);

        $em = $this->getDoctrine()->getManager();

        $attributionForm = $this->createForm(
            new AttributionType($em),
            $attribution,
            array(
                'action' => $this->generateUrl('app_attribution_edit', array('attribution' => $attribution->getId()))
            )
        );

        $attributionForm->handleRequest($request);

        if ($attributionForm->isSubmitted() && $attributionForm->isValid()) {
            $em->persist($attribution);
            $em->flush();

            return new JsonResponse($attribution, Response::HTTP_OK);
        }

        return $this->render('AppBundle:Attribution:attribution_form_modal.html.twig', array(
            'form' => $attributionForm->createView()
        ));
    }


    /**
     * Supprime une attribution
     * @Route("/delete/{attribution}", options={"expose"=true})
     * @ParamConverter("attribution", class="AppBundle:Attribution")
     * @param Attribution $attribution
     * @return JsonResponse
     */
    public function deleteAction(Attribution $attribution)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($attribution);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

}
?>
