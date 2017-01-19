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


use AppBundle\Search\Membre\MembreSearch;
use AppBundle\Search\Membre\MembreSearchType;
use AppBundle\Search\Membre\MembreRepository;

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
 * 3) redirection sur la page de nouveau membre
 *
 * 4) edition rapide du membre/famille possible va formulaire complet
 *
 *
 *
 * Class AddMembreController
 * @package AppBundle\Controller
 * @Route("/intranet/add_membre")
 */
class AddMembreController extends Controller
{

    const MEMBRE_NOM = 'membre_in_progress_nom';
    const MEMBRE_PRENOM = 'membre_in_progress_prenom';
    const FAMILLE_ID = 'membre_in_progress_famille_id';

    /**
     * Permet de repartir à zero dans le processus d'ajout de membre
     *
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

        if ($form->isSubmitted() && $form->isValid()) {

            $session = $request->getSession();

            $nom = $form->get('nom')->getData();
            $prenom = $form->get('prenom')->getData();

            //save nom and prenom in session for futurer use.
            $session->set(AddMembreController::MEMBRE_NOM, $nom);
            $session->set(AddMembreController::MEMBRE_PRENOM, $prenom);
            //reset familleId
            $session->set(AddMembreController::FAMILLE_ID,null);

            //search homonyme
            $membreSearch = new MembreSearch();
            $membreSearch->nom = $nom;
            $membreSearch->prenom = $prenom;
            $resultsMembre = $this->get('app.search')->Membre($membreSearch);

            //search famille with same name
            $familleSearch = new FamilleSearch();
            $familleSearch->nom = $nom;
            $resultsFamille = $this->get('app.search')->Famille($familleSearch);

            if(!empty($resultsMembre))
            {
                //then homonyme exists
                return $this->redirect($this->generateUrl('app_addmembre_homonyme'));
            }

            if(!empty($resultsFamille))
            {
                //then famille exists
                return $this->redirect($this->generateUrl('app_addmembre_famillechoice'));
            }

            //create membre
            return $this->redirect($this->generateUrl('app_addmembre_finish'));

        }

        return $this->render('AppBundle:AddMembre:start.html.twig',
            array('form' => $form->createView(), 'next' => $this->generateUrl('app_addmembre_start')));
    }

    /**
     * @Route("/homonyme", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function HomonymeAction(Request $request)
    {

        //get nom and prenom
        $session = $request->getSession();
        $nom = $session->get(AddMembreController::MEMBRE_NOM);
        $prenom = $session->get(AddMembreController::MEMBRE_PRENOM);

        //search homonyme
        $membreSearch = new MembreSearch();
        $membreSearch->nom = $nom;
        $membreSearch->prenom = $prenom;
        $resultsMembre = $this->get('app.search')->Membre($membreSearch);

        $message = 'Un homonyme existe pour ce membre, voulez-vous continuer?';
        return $this->render('AppBundle:AddMembre:Homonyme.html.twig',
            array(
                'next' => $this->generateUrl('app_addmembre_famillechoice'),
                'message' => $message,
                'homonymes'=>$resultsMembre
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
            //restart process
            return $this->redirect($this->generateUrl('app_addmembre_start'));
        }

        $familleSearch = new FamilleSearch();
        $familleSearch->nom = $nom;
        $searchResults = $this->get('app.search')->Famille($familleSearch);

        if(empty($searchResults))
        {
            $session->set(AddMembreController::FAMILLE_ID, null);
            //direct redirect to next step
            return $this->redirect($this->generateUrl('app_addmembre_finish'));
        }

        $membre = new Membre();

        //cree un formulaire avec les résultats de la recherche en option
        $form = $this->createForm(new MembreFamilleChoiceType($searchResults),$membre);

        $form->handleRequest($request);

        if($form->isValid())
        {
            $familleId = null;
            if($membre->getFamille() != null)
            {
                $familleId =  $membre->getFamille()->getId();
            }

            $session->set(AddMembreController::FAMILLE_ID, $familleId);
            //finish process
            return $this->redirect($this->generateUrl('app_addmembre_finish'));

        }


        return $this->render('AppBundle:AddMembre:FamilleChoice.html.twig',
            array('form'=>$form->createView(),
                'next'=>$this->generateUrl('app_addmembre_famillechoice'),
                'message'=>'Ce nom de famille existe déjà, ce membre fait-il partie d\'une des familles-ci dessous?'
            ));



     }

    /**
     *
     * @Route("/finish", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function finishAction(Request $request) {

        $session = $request->getSession();
        $nom = $session->get(AddMembreController::MEMBRE_NOM,null);
        $prenom = $session->get(AddMembreController::MEMBRE_PRENOM,null);
        $familleId = $session->get(AddMembreController::FAMILLE_ID,null);

        if(is_null($nom) || is_null($prenom))
        {
            return $this->redirect($this->generateUrl('app_addmembre_start'));
        }

        $membre = new Membre();
        $membre->setPrenom($prenom);

        $famille = null;
        if(is_null($familleId))
        {
            $famille = new Famille();
            $famille->setNom($nom);
        }
        else
        {
            $famille = $this->get('app.repository.famille')->findOneBy(array('id'=>$familleId));
        }

        $famille->addMembre($membre);

        //presist new membre and his famille
        $this->get('app.repository.membre')->save($membre);
        $this->get('app.repository.famille')->save($famille);

        /*
         * Plustôt que de rediriger directement sur la page de membre on privilègie
         * le rendu d'un nouveau template qui s'insere bien dans l'ajax qui gere
         * cette page.
         */
        return $this->render('AppBundle:AddMembre:finish.html.twig',
            array('membre'=>$membre));

    }



}
