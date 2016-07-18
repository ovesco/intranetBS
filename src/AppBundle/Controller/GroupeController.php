<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Groupe;
use AppBundle\Form\Groupe\GroupeShowType;
use AppBundle\Form\Groupe\GroupeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils\Menu\Menu;

/**
 * Class GroupeController
 * @package AppBundle\Controller
 *
 * @Route("/intranet/groupe")
 */
class GroupeController extends Controller
{

    /**
     * Page qui affiche la hierarchie des groupes
     *
     * @Route("/gestion", options={"expose"=true})
     * @param Request $request
     * @return Response
     * @Menu("Gestion des groupes", block="structure", order=1, icon="users")
     */
    public function gestionAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $hiestGroupes = $em->getRepository('AppBundle:Groupe')->findHighestGroupes();

        return $this->render('AppBundle:Groupe:page_gestion.html.twig', array(
            'highestGroupes' => $hiestGroupes
        ));
    }

    /**
     * @param $groupe Groupe le groupe
     * @return Response la vue
     *
     * @ParamConverter("groupe", class="AppBundle:Groupe")
     * @Route("/show/{groupe}", options={"expose"=true})
     * @Template("AppBundle:Groupe:page_voir_groupe.html.twig", vars={"groupe"})
     */
    public function showAction($groupe) {

        return array(
            'listing'       => $this->get('listing'),
            'groupe'        => $groupe,
            'groupeForm' => $this->createForm(new GroupeShowType(), $groupe)->createView()
        );
    }

    /**
     * @Route("/edit/{groupe}", options={"expose"=true})
     *
     * @param Request $request
     * @param Groupe $groupe
     * @return Response
     * @ParamConverter("groupe", class="AppBundle:Groupe")
     */
    public function editAction(Groupe $groupe,Request $request)
    {

        //$editedGroupe = new Groupe();
        $editedGroupeForm = $this->createForm(new GroupeShowType(), $groupe);

        $editedGroupeForm->handleRequest($request);

        if($editedGroupeForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            //$groupe->setNom($editedGroupe->getNom());


            $em->flush();

        }

        return $this->redirect($this->generateUrl('app_groupe_gestion'));
    }



    /**
     * @Route("/add", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request)
    {
        $newGroupe = new Groupe();
        $newGroupeForm = $this->createForm(new GroupeType(),$newGroupe);

        $newGroupeForm->handleRequest($request);

        if($newGroupeForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newGroupe);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('app_groupe_gestion'));
    }


    /**
     * @Route("/get_form", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     */
    public function getFormAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {

            $em = $this->getDoctrine()->getManager();
            /*
             * On récupère les infos dans la requete
             */
            $idParent = $request->request->get('idParent');
            $idGroupe = $request->request->get('idGroupe');


            $groupeParent = $em->getRepository('AppBundle:Groupe')->find($idParent);
            $groupe = $em->getRepository('AppBundle:Groupe')->find($idGroupe);

            if($groupe == null)
            {
                /*
                 * ajout d'un nouveaux groupe
                 */
                $groupe = new Groupe();
                $groupe->setParent($groupeParent);

                $groupeForm = $this->createForm(new GroupeType(),$groupe,
                    array('action' => $this->generateUrl('app_groupe_add')));

                return $this->render('AppBundle:Groupe:groupe_modale_form.html.twig',array('form'=>$groupeForm->createView()));

            }
            else
            {
                /*
                 * Modification d'un groupe existant
                 */
                $groupeForm = $this->createForm(new GroupeType(),$groupe,
                    array('action' => $this->generateUrl('app_groupe_edit',array('groupe'=>$idGroupe))));

                return $this->render('AppBundle:Groupe:groupe_modale_form.html.twig',array('form'=>$groupeForm->createView()));
            }




        }
        return new Response();
    }


}