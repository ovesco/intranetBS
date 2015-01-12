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
use AppBundle\Form\FonctionType;
use AppBundle\Entity\Fonction;

class GroupeController extends Controller
{

    /**
     * @param $groupe Groupe le groupe
     * @return Response la vue
     *
     * @paramConverter("groupe", class="AppBundle:Groupe")
     * @route("groupe/voir/{groupe}", name="interne_voir_groupe", options={"expose"=true})
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

    /**
     * Page qui affiche la hierarchie globale
     * @Route("groupe/hierarchie_simple", name="groupe_hierarchie_simple")
     * @Template("Groupe/hierarchieSimple.html.twig")
     */
    public function hierarchieSimpleAction(Request $request) {

        $groupeRepo = $this->getDoctrine()->getRepository('AppBundle:Groupe');
        $hiestGroupes = $groupeRepo->findHighestGroupes();

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

        $fonctions = $this->getDoctrine()->getRepository('AppBundle:Fonction')->findAll();
        $types = $this->getDoctrine()->getRepository('AppBundle:Type')->findAll();

        $fonctionForm = $this->createForm(new FonctionType,new Fonction());

        return array(
            'highestGroupes' =>$hiestGroupes,
            'groupeForm'=>$groupeForm->createView(),
            'fonctions' => $fonctions,
            'types'=>$types,
            'fonctionForm' => $fonctionForm->createView(),
            );
    }

}