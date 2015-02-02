<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Groupe;
use AppBundle\Entity\GroupeModel;
use AppBundle\Form\GroupeModelType;
use AppBundle\Form\GroupeType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\FonctionType;
use AppBundle\Entity\Fonction;

/**
 * Class StructureController
 * @package AppBundle\Controller
 *
 * @Route("/structure")
 */
class StructureController extends Controller
{
    /**
     * Page qui affiche la hierarchie des groupes
     *
     * @Route("/hierarchie", name="structure_hierarchie_groupe", options={"expose"=true})
     * @param Request $request
     * @return Response
     *
     */
    public function hierarchieAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $hiestGroupes = $em->getRepository('AppBundle:Groupe')->findHighestGroupes();

        return $this->render('AppBundle:Structure:page_hierarchie.html.twig', array(
            'highestGroupes' => $hiestGroupes
        ));


    }








    /**
     * Page qui affiche les fonctions
     *
     * @Route("/gestion_fonction", name="structure_gestion_fonctions", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     *
     */
    public function gestionFonctionAction(Request $request) {

        $em = $this->getDoctrine()->getManager();


        //retourne toutes les fonctions
        $fonctions = $em->getRepository('AppBundle:Fonction')->findAll();

        return $this->render('AppBundle:Structure:page_gestionFonction.html.twig',array(
            'fonctions' =>$fonctions));


    }

    /**
     * Page qui affiche les models de groupes
     *
     * @Route("/gestion_groupe_model", name="structure_gestion_groupe_model", options={"expose"=true})
     * @param Request $request
     * @return Response
     *
     */
    public function gestionGroupeModelAction(Request $request) {

        $em = $this->getDoctrine()->getManager();

        if($request->isXmlHttpRequest())
        {

            /*
             * On envoie le formulaire en modal
             */
            $id = $request->request->get('idGroupeModel');

            $model = null;
            $modelForm = null;
            if($id == null)
            {
                /*
                 * Ajout
                 */
                $model = new GroupeModel();
                $modelForm = $this->createForm(new GroupeModelType(),$model);
            }
            else
            {
                $model = $em->getRepository('AppBundle:GroupeModel')->find($id);
                $modelForm = $this->createForm(new GroupeModelType(),$model);
                $modelForm->get('savedName')->setData($model->getNom()); //formulaire pour l'update de type, il contient le nom
            }

            return $this->render('AppBundle:GroupeModel:groupe_model_modale_form.html.twig',array('form'=>$modelForm->createView()));

        }
        else
        {
            /*
             * Soit la page est demandée, soit un formulaire est soumis
             */
            $model = new GroupeModel();
            $modelForm = $this->createForm(new GroupeModelType,$model);

            if($request->request->has($modelForm->getName()))
            {
                $modelForm->handleRequest($request);

                if ($modelForm->isValid()) {


                    $oldName = $modelForm->get('savedName')->getData();

                    $mdoelInDB = $em->getRepository('AppBundle:GroupeModel')->findOneBy(array('nom'=>$oldName));

                    if($mdoelInDB == null)
                    {
                        /*
                         * Nouveaux type
                         */
                        $em->persist($model);
                    }
                    else
                    {
                        /*
                         * Update d'un type
                         * On copie les infos dans l'objet déjà existant
                         */
                        $mdoelInDB->setNom($model->getNom());
                        $mdoelInDB->setFonctionChef($model->getFonctionChef());
                        $mdoelInDB->setFonctions($model->getFonctions());
                        $mdoelInDB->setAffichageEffectifs($model->isAffichageEffectifs());


                    }
                    $em->flush();

                    return $this->redirect($this->generateUrl('structure_gestion_groupe_model'));
                }
            }

            //retourne toutes les fonctions
            $models = $em->getRepository('AppBundle:GroupeModel')->findAll();

            return $this->render('AppBundle:Structure:page_gestionGroupeModel.html.twig',array(
                'models' =>$models));
        }




    }


}