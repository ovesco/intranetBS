<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Famille;
use AppBundle\Entity\Membre;
use AppBundle\Form\Membre\MembreWithoutFamilleType;
use AppBundle\Form\Membre\MembreFamilleChoiceType;
use AppBundle\Form\Membre\MembreNomPrenomType;
use AppBundle\Form\Membre\MembreWithFamilleType;
use AppBundle\Search\Famille\FamilleSearch;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Utils\ListUtils\ListStorage;
use Doctrine\ORM\EntityManager;


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



/**
 * Principe de fonctionnnement.
 *
 * 1) nom prenom:
 *
 * 2) recherche de la famille avec le même nom de famille
 *
 * 2.a) choix de famille à faire en fonction du résultat de la recherche
 *
 * 2.b) aucune famille trouvée avec ce nom, on crée une nouvelle famille
 *
 * 3) forumaire du membre
 *
 *
 *
 * Class AddMembreController
 * @package AppBundle\Controller
 * @Route("/add_membre")
 */
class AddMembreController extends Controller
{

    const MEMBRE_NOM = 'membre_in_progress_nom';
    const MEMBRE_PRENOM = 'membre_in_progress_prenom';
    const FAMILLE_ID = 'membre_in_progress_famille_id';

    /**
     * @Route("/reset", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function resetAction(Request $request)
    {
        $session = $request->getSession();
        $session->set(AddMembreController::MEMBRE_NOM,null);
        $session->set(AddMembreController::MEMBRE_PRENOM,null);
        $session->set(AddMembreController::FAMILLE_ID,null);
        return $this->redirect($this->generateUrl('app_membre_add'));
    }


    /**
     * @Route("/start", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function startAction(Request $request)
    {
        $form = $this->createForm(new MembreNomPrenomType(),new Membre());

        //recupere le nom si il a deja été stocké en mémoire (action: précédent)
        // $form->get('nom')->setData($session->get(AddMembreController::FAMILLE_NAME, null));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $session = $request->getSession();

            $nom = $form->get('nom')->getData();
            $prenom = $form->get('prenom')->getData();

            $session->set(AddMembreController::MEMBRE_NOM, $nom);
            $session->set(AddMembreController::MEMBRE_PRENOM, $prenom);

            $membreSearch = new MembreSearch();
            $membreSearch->nom = $nom;
            $membreSearch->prenom = $prenom;
            $results = $this->get('app.search')->Membre($membreSearch);
            if(!empty($results))
            {
                return $this->redirect($this->generateUrl('app_addmembre_homonyme'));
            }
            return $this->redirect($this->generateUrl('app_addmembre_famillechoice'));

        }
        return $this->render('AppBundle:Membre/AddForm:Start.html.twig',
            array('form' => $form->createView(), 'next' => $this->generateUrl('app_addmembre_start')));
    }

    /**
     * @Route("/homonyme", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function HomonymeAction(Request $request)
    {
        $message = 'Un homonyme existe pour ce membre, voulez-vous continuer?';
        return $this->render('AppBundle:Membre/AddForm:Homonyme.html.twig',
            array(
                'next' => $this->generateUrl('app_addmembre_famillechoice'),
                'message' => $message
            ));
    }


     /**
      *
      * @Route("/famille_choice", options={"expose"=true})
      * @param Request $request
      * @return Response
      */
    public function familleChoiceAction(Request $request) {

        $session = $request->getSession();

        $nom = $session->get(AddMembreController::MEMBRE_NOM,null);

        if(is_null($nom))
        {
            return $this->redirect($this->generateUrl('app_addmembre_start'));
        }

        $familleSearch = new FamilleSearch();
        $familleSearch->nom = $nom;
        $matchedFamilles = $this->get('app.search')->Famille($familleSearch);

        if(empty($matchedFamilles))
        {
            $session->set(AddMembreController::FAMILLE_ID, null);
            //direct redirect to next step
            return $this->redirect($this->generateUrl('app_addmembre_membre'));
        }
        else
        {
            $membre = new Membre();
            //choix d'une famille
            $form = $this->createForm(new MembreFamilleChoiceType($matchedFamilles),$membre);

            $form->handleRequest($request);

            if($form->isValid())
            {
                if($membre->getFamille() != null)
                {
                    $familleId =  $membre->getFamille()->getId();
                }
                else
                {
                    $familleId = null;
                }

                $session->set(AddMembreController::FAMILLE_ID, $familleId);
                //redirect to check famille
                return $this->redirect($this->generateUrl('app_addmembre_membre'));

            }

            /** @var ListStorage $sessionContainer */
            $sessionContainer = $this->get('list_storage');
            $sessionContainer->setRepository(ListKey::FAMILLE_SEARCH_RESULTS_ADD_MEMBRE,'AppBundle:Famille');
            $sessionContainer->setObjects(ListKey::FAMILLE_SEARCH_RESULTS_ADD_MEMBRE,$matchedFamilles);

            return $this->render('AppBundle:Membre/AddForm:FamilleChoice.html.twig',
                array('form'=>$form->createView(),
                    'next'=>$this->generateUrl('app_addmembre_famillechoice'),
                    'list_key'=>ListKey::FAMILLE_SEARCH_RESULTS_ADD_MEMBRE,
                    'message'=>'Ce nom de famille existe déjà, ce membre fait-il partie d\'une des familles-ci dessous?'
                ));
        }


     }

    /**
     *
     * @Route("/membre", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function membreAction(Request $request) {

        $session = $request->getSession();
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $nom = $session->get(AddMembreController::MEMBRE_NOM,null);
        $prenom = $session->get(AddMembreController::MEMBRE_PRENOM,null);
        $familleId = $session->get(AddMembreController::FAMILLE_ID,null);
        if(is_null($nom) || is_null($prenom))
        {
            return $this->redirect($this->generateUrl('app_addmembre_start'));
        }

        $form = null;
        $membre = new Membre();
        $membre->setPrenom($prenom);
        $famille = null;
        $template = null;
        if(!is_null($familleId))
        {
            $famille = $em->getRepository('AppBundle:Famille')->find($familleId);
            $membre->setFamille($famille);
            $form = $this->createForm(new MembreWithoutFamilleType(),$membre);
            $template = 'AppBundle:Membre/AddForm:AddMembreWithoutFamille.html.twig';
        }
        else
        {
            $famille = new Famille();
            $famille->setNom($nom);
            $membre->setFamille($famille);
            $form = $this->createForm(new MembreWithFamilleType(),$membre);
            $template = 'AppBundle:Membre/AddForm:AddMembre.html.twig';
        }


        $form->handleRequest($request);

        if($form->isValid())
        {
            $em->persist($membre);
            $em->flush();
            return $this->render('AppBundle:Membre/AddForm:End.html.twig',array('membre'=>$membre));
        }

        return $this->render($template,
            array('form'=>$form->createView(),
                'famille'=>$famille,
                'next'=> $this->generateUrl('app_addmembre_membre'),
            ));



    }



}
