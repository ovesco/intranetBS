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
        $hiestGroupes = $this->get('app.repository.groupe')->findHighestGroupes();

        return $this->render('AppBundle:Groupe:page_gestion.html.twig', array(
            'highestGroupes' => $hiestGroupes
        ));
    }

    /**
     * @param $groupe Groupe le groupe
     * @return array Para a render dans le template
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
     * @param $groupe Groupe le groupe
     * @return Response PDF of the group members
     *
     * @ParamConverter("groupe", class="AppBundle:Groupe")
     * @Route("/export-pdf/{groupe}", options={"expose"=true})
     */
    public function exportPdfAction($groupe)
    {

        $html = $this->renderView('@App/Groupe/pdf.html.twig', array(
                'group' => $groupe
            )
        );

        //return new Response($html);

        $snappy = $this->get('knp_snappy.pdf');

        $snappy->setOption('header-html', 'Header');
        $snappy->setOption('footer-html', '[page]');

        $pdf = $snappy->getOutputFromHtml($html, array(
            'enable-javascript' => true,
            'javascript-delay' => 1000,
            'no-stop-slow-scripts' => true,
            'no-background' => false,
            'lowquality' => false,
            'encoding' => 'UTF-8',
            'images' => true,
            'cookie' => array(),
            'dpi' => 300,
            'image-dpi' => 300,
            'enable-external-links' => true,
            'enable-internal-links' => true
        ));

        return new Response($pdf, 200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $groupe->getNom() . '.pdf"'
            )
        );
    }

    /**
     * @param $groupe Groupe le groupe
     * @return Response Excel of the group members
     *
     * @ParamConverter("groupe", class="AppBundle:Groupe")
     * @Route("/export-excel/{groupe}", options={"expose"=true})
     */
    public function exportExcelAction($groupe)
    {


    }


    /**
     * @param $groupe Groupe le groupe
     * @return Response Excel of the group members
     *
     * @ParamConverter("groupe", class="AppBundle:Groupe")
     * @Route("/export-etiquettes/{groupe}/{rowCount}/{colCount}/{fontSize}", options={"expose"=true})
     */
    public function exportEtiquettesAction($groupe, $rowCount, $colCount, $fontSize)
    {

        $html = $this->renderView('@App/Groupe/etiquettes.html.twig', array(
                'group' => $groupe,
                'corrected_page_height' => 41, //cm
                'corrected_page_width' => 29, //cm
                'cell_vertical_count' => $rowCount,
                'cell_horizontal_count' => $colCount,
                'font_size' => $fontSize / 100
            )
        );

        //return new Response($html);

        $snappy = $this->get('knp_snappy.pdf');


        $pdf = $snappy->getOutputFromHtml($html, array(
            'no-background' => true,
            'encoding' => 'UTF-8',
            'images' => true,
            'cookie' => array(),
            'dpi' => 300,
            'image-dpi' => 300,
            'enable-external-links' => true,
            'enable-internal-links' => true,
            'margin-top' => 0,
            'margin-right' => 0,
            'margin-bottom' => 0,
            'margin-left' => 0,
            'page-size' => 'A4'
        ));

        return new Response($pdf, 200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $groupe->getNom() . '.pdf"'
            )
        );
    }

    /**
     * @param $groupe Groupe le groupe
     * @return Response Excel of the group members in REGA format
     *
     * @ParamConverter("groupe", class="AppBundle:Groupe")
     * @Route("/export-rega/{groupe}", options={"expose"=true})
     */
    public function exportRegaAction($groupe)
    {


    }


    /**
     * @Route("/edit/{groupe}", options={"expose"=true})
     * @Template("AppBundle:Groupe:modal_form.html.twig", vars={"groupe"})
     * @param Request $request
     * @param Groupe $groupe
     * @return Response
     * @ParamConverter("groupe", class="AppBundle:Groupe")
     */
    public function editAction(Groupe $groupe,Request $request)
    {
        $form = $this->createForm(new GroupeType(), $groupe,
            array('action' => $this->generateUrl('app_groupe_edit',array('groupe'=>$groupe->getId()))));

        $form->handleRequest($request);

        if($form->isValid())
        {
            $this->get('app.repository.groupe')->save($groupe);
            return $this->redirect($this->generateUrl('app_groupe_gestion'));
        }

        return array('form'=>$form->createView());

    }



    /**
     * @Route("/add", options={"expose"=true})
     * @Template("AppBundle:Groupe:page_voir_groupe.html.twig", vars={"groupe"})
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