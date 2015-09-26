<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Groupe;
use AppBundle\Form\VoirGroupeType;
use AppBundle\Form\GroupeType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GroupeController
 * @package AppBundle\Controller
 *
 * @Route("/groupe")
 */
class GroupeController extends Controller
{

    /**
     * @param $groupe Groupe le groupe
     * @return Response la vue
     *
     * @paramConverter("groupe", class="AppBundle:Groupe")
     * @route("/voir/{groupe}", name="interne_voir_groupe", options={"expose"=true})
     * @Template("AppBundle:Groupe:page_voir_groupe.html.twig", vars={"groupe"})
     */
    public function showGroupeAction($groupe) {

        return array(
            'listing'       => $this->get('listing'),
            'groupe'        => $groupe,
            'groupeForm'    => $this->createForm(new VoirGroupeType(), $groupe)->createView()
        );
    }

    /**
     * @Route("/edit/{groupe}", name="groupe_edit", options={"expose"=true})
     *
     * @param Request $request
     * @param Groupe $groupe
     * @return Response
     * @ParamConverter("groupe", class="AppBundle:Groupe")
     */
    public function editGroupeAction(Groupe $groupe,Request $request)
    {

        //$editedGroupe = new Groupe();
        $editedGroupeForm = $this->createForm(new GroupeType(),$groupe);

        $editedGroupeForm->handleRequest($request);

        if($editedGroupeForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            //$groupe->setNom($editedGroupe->getNom());


            $em->flush();

        }

        return $this->redirect($this->generateUrl('structure_hierarchie_groupe'));
    }



    /**
     * @Route("/add", name="groupe_add", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     */
    public function addGroupeAction(Request $request)
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

        return $this->redirect($this->generateUrl('structure_hierarchie_groupe'));
    }


    /**
     * @Route("/get_form_modale", name="groupe_get_form_modale", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     */
    public function getGroupeFormAjaxAction(Request $request)
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
                    array('action' => $this->generateUrl('groupe_add')));

                return $this->render('AppBundle:Groupe:groupe_modale_form.html.twig',array('form'=>$groupeForm->createView()));

            }
            else
            {
                /*
                 * Modification d'un groupe existant
                 */
                $groupeForm = $this->createForm(new GroupeType(),$groupe,
                    array('action' => $this->generateUrl('groupe_edit',array('groupe'=>$idGroupe))));

                return $this->render('AppBundle:Groupe:groupe_modale_form.html.twig',array('form'=>$groupeForm->createView()));
            }




        }
        return new Response();
    }


}
