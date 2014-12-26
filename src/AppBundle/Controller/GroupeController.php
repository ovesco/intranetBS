<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Groupe;
use AppBundle\Form\GroupeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GroupeController extends Controller
{

    /**
     * @param $groupe Groupe le groupe
     * @return Response la vue
     *
     * @paramConverter("groupe", class="AppBundle:Groupe")
     * @route("groupe/voir-groupe/{groupe}", name="interne_voir_groupe", options={"expose"=true})
     * @Template("Groupe/voir_groupe.html.twig", vars={"groupe"})
     */
    public function showGroupeAction($groupe) {

        return array(
            'listing' => $this->get('listing'),
            'groupe'  => $groupe
        );
    }

    /**
     * Page qui affiche la hierarchie globale
     * @Route("groupe/hierarchie", name="groupe_hierarchie")
     * @Template("Groupe/hierarchie.html.twig")
     */
    public function hierarchieAction(Request $request) {

        $groupeRepo = $this->getDoctrine()->getRepository('AppBundle:Groupe');
        $hierarchie = $groupeRepo->findJSONHierarchie();

        $groupe     = new Groupe();
        $groupeForm = $this->createForm(new GroupeType, $groupe);


        /*
         * On a peut-être tenté d'ajouter un groupe, dans ce cas on va valider le nouveau groupe, puis rediriger vers
         * la page de celui-ci
         */
        $groupeForm->handleRequest($request);

        if ($groupeForm->isValid()) {

            //On récupère le groupe parent
            $parent = $groupeRepo->find($request->request->get('groupe_id'));
            $groupe->setParent($parent);

            $this->getDoctrine()->getManager()->persist($groupe);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('interne_voir_groupe', array('groupe' => $groupe->getId())));
        }


        return array(
            'clientData' => json_encode($hierarchie),
            'groupeForm' => $groupeForm->createView()
        );
    }
}