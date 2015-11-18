<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Famille;
use AppBundle\Entity\Membre;
use AppBundle\Form\AddMembre\FamilleCheckMembreType;
use AppBundle\Form\AddMembreType;
use AppBundle\Form\AddMembre\BaseMembreType;
use AppBundle\Form\VoirMembreType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Utils\ListUtils\ListStorage;

use AppBundle\Form\AddMembre\FamilleChoiceMembreType;

use AppBundle\Search\MembreSearch;
use AppBundle\Search\MembreSearchType;
use AppBundle\Search\MembreRepository;

use AppBundle\Search\Mode;

/* annotations */
use AppBundle\Utils\Menu\Menu;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AppBundle\Utils\ListUtils\ListKey;

use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Form\AddMembre\FamilleAddMembreType;

use AppBundle\Form\AddMembre\InfosMembreType;


/**
 * Principe de fonctionnnement.
 *
 * 1) nom prenom et sexe:
 *      mimimum nécaissaire pour persister le membre ainsi que les
 *      entité liée (débiteur, receiver, etc)
 *
 * 2) recherche de la famille avec le même nom de famille
 *
 * 2.a) choix de famille à faire en fonction du résultat de la recherche
 *      -> puis formulaire de la famille pour vérifier si les infos sont corrects
 *
 * 2.b) aucune famille trouvée avec ce nom, on crée une nouvelle famille
 *
 * 3) information sur le membre (iban, naissance, etc)
 *
 * 4) ajout du membre dans un groupe.
 *
 *
 *
 * Class AddMembreController
 * @package AppBundle\Controller
 * @Route("/add_membre")
 */
class AddMembreController extends Controller {


    const MEMBRE_ID = 'membre_in_progress_id';
    const FAMILLE_NAME = 'membre_in_progress_famille_name';

    /**
     * @Route("/reset", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function resetAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        $membreId = $session->get(AddMembreController::MEMBRE_ID,null);
        if(!is_null($membreId))
        {
            $membre = $em->getRepository('AppBundle:Membre')->find($membreId);
            $em->remove($membre);
        }

        $session->set(AddMembreController::MEMBRE_ID,null);
        $session->set(AddMembreController::FAMILLE_NAME,null);

        return $this->redirect($this->generateUrl('app_membre_add'));
    }


    /**
     * @Route("/nom_prenom_sexe", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function BaseAction(Request $request) {

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        $membre = null;
        $membreId = $session->get(AddMembreController::MEMBRE_ID,null);


        if(!is_null($membreId))
        {
            $membre = $em->getRepository('AppBundle:Membre')->find($membreId);
        }
        else{
            $membre = new Membre();
        }

        $form = $this->createForm(new BaseMembreType(), $membre);

        //recupere le nom si il a deja été stocké en mémoire (action: précédent)
        $form->get('nom')->setData($session->get(AddMembreController::FAMILLE_NAME, null));

        $form->handleRequest($request);

        if($form->isValid())
        {
            $em->persist($membre);
            $em->flush();

            $session->set(AddMembreController::MEMBRE_ID,$membre->getId());
            $session->set(AddMembreController::FAMILLE_NAME, $form->get('nom')->getData());

            return $this->redirect($this->generateUrl('app_addmembre_famillechoice'));

        }
        return $this->render('AppBundle:Membre/AddForm:Base.html.twig',
            array('form'=>$form->createView(),'next'=>$this->generateUrl('app_addmembre_base')));
    }


    /**
     *
     * @Route("/famille_choice", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function familleChoiceAction(Request $request) {

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        $membreId = $session->get(AddMembreController::MEMBRE_ID,null);

        /** @var Membre $membre */
        $membre = null;
        if(!is_null($membreId))
        {
            $membre = $em->getRepository('AppBundle:Membre')->find($membreId);
        }
        else{
            //a new membre must be persisted before choising a famille...
            return $this->redirect($this->generateUrl('app_addmembre_base'));
        }

        $familleName = $session->get(AddMembreController::FAMILLE_NAME);
        $matchedFamilles = $this->get('elastic_to_doctrine')->convert('fos_elastica.finder.search.famille','AppBundle:Famille',$familleName);


        if(empty($matchedFamilles))
        {
            //direct redirect to add famille
            return $this->redirect($this->generateUrl('app_addmembre_familleadd'));
        }
        else
        {
            $matchedFamilles = $this->get('fos_elastica.finder.search.famille')->find($familleName);

            //choix d'une famille
            $form = $this->createForm(new FamilleChoiceMembreType($matchedFamilles),$membre);

            $form->handleRequest($request);

            if($form->isValid())
            {

                $em->persist($membre);
                $em->flush();

                //redirect to check famille
                return $this->redirect($this->generateUrl('app_addmembre_famillecheck'));

            }

            return $this->render('AppBundle:Membre/AddForm:FamilleChoice.html.twig',
                array('form'=>$form->createView(),
                    'membre'=>$membre,
                    'previous'=>$this->generateUrl('app_addmembre_base'),
                    'next'=>$this->generateUrl('app_addmembre_famillechoice')
                ));
        }


    }



    /**
     * @Route("/famille_add", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function FamilleAddAction(Request $request) {

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        $membreId = $session->get(AddMembreController::MEMBRE_ID,null);

        /** @var Membre $membre */
        $membre = null;
        if(!is_null($membreId))
        {
            $membre = $em->getRepository('AppBundle:Membre')->find($membreId);
        }
        else{
            //a new membre must be persisted before choising a famille...
            return $this->redirect($this->generateUrl('app_addmembre_base'));
        }

        $famille = new Famille();
        $famille->setNom($session->get(AddMembreController::FAMILLE_NAME));
        $membre->setFamille($famille);

        $form = $this->createForm(new FamilleAddMembreType(),$membre);

        $form->handleRequest($request);

        if($form->isValid())
        {
            $em->persist($membre);
            $em->flush();

            return $this->redirect($this->generateUrl('app_addmembre_infos'));

        }

        return $this->render('AppBundle:Membre/AddForm:FamilleAdd.html.twig',
            array('form'=>$form->createView(),
                'membre'=>$membre,
                'previous'=>$this->generateUrl('app_addmembre_base'),
                'next'=>$this->generateUrl('app_addmembre_familleadd')
            ));


    }

    /**
     * @Route("/famille_check", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function FamilleCheckAction(Request $request) {

        //check info famille form
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        $membreId = $session->get(AddMembreController::MEMBRE_ID,null);

        /** @var Membre $membre */
        $membre = null;
        if(!is_null($membreId))
        {
            $membre = $em->getRepository('AppBundle:Membre')->find($membreId);
        }
        else{
            //a new membre must be persisted before choising a famille...
            return $this->redirect($this->generateUrl('app_addmembre_base'));
        }


        //todo faire en sorte que on puisse pas modifier le nom de famille
        $form = $this->createForm(new FamilleCheckMembreType(),$membre);

        $form->handleRequest($request);

        if($form->isValid())
        {
            $em->persist($membre);
            $em->flush();

            return $this->redirect($this->generateUrl('app_addmembre_infos'));

        }

        return $this->render('AppBundle:Membre/AddForm:FamilleAdd.html.twig',
            array('form'=>$form->createView(),
                'membre'=>$membre,
                'previous'=>$this->generateUrl('app_addmembre_base'),
                'next'=>$this->generateUrl('app_addmembre_famillecheck')

            ));


    }

    /**
     * @Route("/infos", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function InfosAction(Request $request) {

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        $membreId = $session->get(AddMembreController::MEMBRE_ID,null);

        $membre = null;
        if(!is_null($membreId))
        {
            $membre = $em->getRepository('AppBundle:Membre')->find($membreId);
        }
        else{
            //a new membre must be persisted before choising a famille...
            return $this->redirect($this->generateUrl('app_addmembre_base'));
        }


        //todo faire en sorte que on puisse pas modifier le nom de famille
        $form = $this->createForm(new InfosMembreType(),$membre);

        if($form->isValid())
        {

        }

        return $this->render('AppBundle:Membre/AddForm:Infos.html.twig',
            array('form'=>$form->createView(),
                'membre'=>$membre,
                'previous'=>$this->generateUrl('app_addmembre_base'),

            ));



    }


}
