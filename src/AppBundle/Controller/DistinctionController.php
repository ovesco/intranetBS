<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Email;
use AppBundle\Entity\ObtentionDistinction;
use AppBundle\Entity\Telephone;
use AppBundle\Form\AddEmailType;
use AppBundle\Form\AddTelephoneType;
use AppBundle\Entity\Contact;
use AppBundle\Form\ObtentionDistinctionType;
use AppBundle\Form\TelephoneType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ContactController
 * @package AppBundle\Controller
 *
 * @Route("/distinction/")
 */
class DistinctionController extends Controller{

    /**
     * @route("remove/{distinction}", name="interne_structure_remove_distinction", options={"expose"=true})
     * @paramConverter("distinction", class="AppBundle:ObtentionDistinction")
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
     * @route("add-obtention", name="interne_add_obtention_distinction")
     * @param Request $request
     * @return Response
     */
    public function renderModalOrPersistAction(Request $request) {

        $obtention = new ObtentionDistinction();
        $form      = $this->createForm(new ObtentionDistinctionType(), $obtention);

        $form->handleRequest($request);

        if($form->isValid()) {

            $em      = $this->getDoctrine()->getManager();
            $repo    = $em->getRepository('AppBundle:Membre');
            $membres = explode(",", $form->get('membres')->getData());

            foreach($membres as $id) {

                $membre = $repo->find($id);

                $membre->addDistinction($obtention);
                $em->persist($membre);
            }

            $em->flush();

            return $this->redirect(  $request->headers->get('referer')  );
        }

        return $this->render('AppBundle:Modales:modal_add_obtention.html.twig', array('form' => $form->createView()));
    }
}