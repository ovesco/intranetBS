<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Membre;
use AppBundle\Entity\ObtentionDistinction;
use AppBundle\Form\ObtentionDistinctionType;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ContactController
 * @package AppBundle\Controller
 *
 * @Route("/intranet/distinction/")
 */
class DistinctionController extends Controller{

    /**
     * @Route("remove/{distinction}", name="interne_structure_remove_distinction", options={"expose"=true})
     * @ParamConverter("distinction", class="AppBundle:ObtentionDistinction")
     * @param $distinction
     * @return JsonResponse
     */
    public function removeObtentionDistinctionAction($distinction) {

        $em = $this->getDoctrine()->getManager();

        $em->remove($distinction);
        $em->flush();

        return new JsonResponse();
    }

    /**
     * Génère la modale pour ajouter une distinction à un ou plusieurs membres
     * @Route("add-obtention", name="interne_add_obtention_distinction")
     * @param Request $request
     * @return Response
     */
    public function renderModalOrPersistAction(Request $request) {


        $obtention = new ObtentionDistinction();
        $form      = $this->createForm(new ObtentionDistinctionType(), $obtention);

        $form->handleRequest($request);

        if($form->isValid()) {

            /** @var EntityManager $em */
            $em      = $this->getDoctrine()->getManager();
            $repo    = $em->getRepository('AppBundle:Membre');
            $membres = explode(",", $form->get('membres')->getData());

            foreach($membres as $id) {

                /** @var Membre $membre */
                $membre = $repo->find($id);

                $membre->addDistinction($obtention);
                $em->persist($membre);
            }

            $em->flush();

            return $this->redirect(  $request->headers->get('referer')  );
        }


        return $this->render('AppBundle:ObtentionDistinction:modal_add_obtention.html.twig', array('form' => $form->createView()));
    }
}