<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Famille;
use AppBundle\Entity\Membre;
use AppBundle\Form\Membre\MembreShowType;
use AppBundle\Form\Membre\MembreEditType;
use AppBundle\Search\Membre\MembreSearch;
use AppBundle\Search\Membre\MembreSearchType;
use AppBundle\Search\Mode;
use AppBundle\Utils\ListUtils\ListKey;
use AppBundle\Utils\ListUtils\ListStorage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils\Menu\Menu;
use AppBundle\Search\Membre\MembreRepository;


/**
 * Class MembreController
 * @package AppBundle\Controller
 * @Route("/intranet/membre")
 */
class MembreController extends Controller {


    const SEARCH_RESULTS = "session_results";

    /**
     * Affiche la page d'ajout de membre
     * (Le formulaire d'ajout de membre est géré par le controller AddMembreController)
     *
     * @Route("/add")
     * @Menu("Ajouter un membre",block="database",order=1, icon="add", expanded=true)
     * @param Request $request
     * @return Response
     * @Template("AppBundle:Membre:page_add.html.twig")
     */
    public function addAction(Request $request) {
        return array();
    }

    /**
     * todo: je crois que cette fonciton n'est plus utilisée...a checké (uffer, 16 nov 2015)
     *
     * Cette fonction retourne une proprieté d'un membre donné par son id. la proprieté doit être du type param1__param2...
     * (getFamille()->getAdresse())
     * @param $membre Membre le membre
     * @param $property la proprieté à atteindre
     * @return mixed proprieté
     *
     * @Route("ajax/get-property/{membre}/{property}", options={"expose"=true})
     * @ParamConverter("membre", class="AppBundle:Membre")
     */
    public function getMembrePropertyAction(Membre $membre, $property) {

        $accessor = $this->get('accessor');
        $serializer = $this->get('jms_serializer');

        $data = $serializer->serialize($accessor->getProperty($membre, $property), 'json');
        return new JsonResponse($data);
    }

    /**
     * @Route("/show/{membre}", options={"expose"=true}, requirements={"membre" = "\d+"})
     * @ParamConverter("membre", class="AppBundle:Membre")
     * @param Request $request
     * @param Membre $membre
     * @return Response
     * @Template("AppBundle:Membre:page_show.html.twig")
     */
    public function showAction(Request $request, Membre $membre) {

        $membreForm = $this->createForm(MembreShowType::class, $membre);

        return array(
            'membre'            => $membre,
            'listing'           => $this->get('listing'),
            'membreForm'        => $membreForm->createView(),
        );
    }

    /**
     * @Route("/edit/{membre}", options={"expose"=true}, requirements={"membre" = "\d+"})
     * @ParamConverter("membre", class="AppBundle:Membre")
     * @param Request $request
     * @param Membre $membre
     * @return Response
     * @Template("AppBundle:Membre:page_edit.html.twig")
     */
    public function editAction(Request $request, Membre $membre) {

        $form = $this->createForm(MembreEditType::class,$membre);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->get('app.repository.membre')->save($membre);
            return $this->redirect($this->generateUrl('app_membre_show',array('membre'=>$membre->getId())));
        }

        return array(
            'membre' => $membre,
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/show_pdf/{membre}", requirements={"membre" = "\d+"})
     * @ParamConverter("membre", class="AppBundle:Membre")
     * @param Request $request
     * @param Membre $membre
     * @return Response
     */
    public function toPdfAction(Request $request, Membre $membre)
    {

        $html = $this->render('@App/Membre/pdf_show.html.twig', array(
                'membre' => $membre,
                'listing' => $this->get('listing'),
            )
        );

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type' => 'application/pdf'
                // 'Content-Disposition' => 'attachment; filename="file.pdf"'
            )
        );
    }

    /**
     * @param $membre membre le membre
     * @param $type string 'attribution' ou 'distinction'
     * @param $obj int l'id de l'attribution ou distinction
     * @return jsonresponse
     * @Route("/ajax/remove-attribution-or-distinction/{membre}/{type}/{obj}", name="app_membre_ajax_remove_attr_dist", options={"expose"=true})
     * @ParamConverter("membre", class="AppBundle:Membre")
     *
     * TODO : pourquoi pas removeAttributionAction et removeDistinctionAction ?
     */
    public function removeAttributionOrDistinctionAction(Membre $membre, $type, $obj) {

        $em   = $this->getDoctrine()->getManager();
        $enti = $em->getRepository('AppBundle:' . $type)->find($obj);

        $func = '';
        if($type == 'Attribution')
            $func = 'removeAttribution';
        else $func = 'removeDistinction';

        $membre->$func($enti);
        $em->persist($membre);
        $em->flush();

        return new JsonResponse(1);
    }

    /**
     * Vérifie si un numéro BS est déjà attribué ou pas
     * @param $numero le numéro BS
     * @return boolean
     * @Route("/ajax/verify-numero-bs/{numero}", name="app_membre_ajax_verify_numero_bs", options={"expose"=true}, requirements={"numero" = "\d+"})
     */
    public function isNumeroBsTakenAction($numero) {

        $num = $this->getDoctrine()->getRepository('AppBundle:Membre')->findByNumeroBs($numero);

        if($num == null) return new JsonResponse(false);
        else return new JsonResponse(true);
    }

    /**
     * Permet de modifier la famille d'un membre
     * @param $membre membre le membre
     * @param $famille famille la famille
     * @return jsonresponse
     * @ParamConverter("membre", class="AppBundle:Membre")
     * @ParamConverter("famille", class="AppBundle:Famille")
     * @Route("/ajax/modify-famille/{membre}/{famille}", name="app_membre_modify_famille", options={"expose"=true})
     *
     * TODO: il faut changer la famille du membre et faire un update, doctrine gère le reste. Cette fonction devrait s'appeler setFamilleAction
     */
    public function modifyFamilleAction(Membre $membre, Famille $famille) {

        $em = $this->getDoctrine()->getManager();

        $old = $membre->getFamille();
        $old->removeMembre($membre);
        $famille->addMembre($membre);

        $em->persist($old);
        $em->persist($famille);
        $em->flush();

        return new JsonResponse('');
    }

    /**
     * Affiche la page permettant de lancer une recherche
     *
     * @Route("/search")
     * @Menu("Rechercher un membre",block="database",order=2, icon="search", expanded=true)
     * @Template("AppBundle:Membre:page_search.html.twig")
     */
    public function searchAction(Request $request)
    {

        $membreSearch = new MembreSearch();
        $membreForm = $this->createForm(new MembreSearchType(),$membreSearch);


        /** @var ListStorage $sessionContainer */
        $sessionContainer = $this->get('list_storage');
        $sessionContainer->setRepository(ListKey::MEMBRES_SEARCH_RESULTS,'AppBundle:Membre');

        $membreForm->handleRequest($request);

        if ($membreForm->isValid()) {

            $elasticaManager = $this->container->get('fos_elastica.manager');

            /** @var MembreRepository $repository */
            $repository = $elasticaManager->getRepository('AppBundle:Membre');

            $results = $repository->search($membreSearch);

            //$results = $this->container->get('app.search')->Membre($membreSearch);

            //get the search mode
            $mode = $membreForm->get("mode")->getData();
            switch($mode)
            {
                case Mode::INCLUDE_PREVIOUS: //include new results with the previous
                    $sessionContainer->addObjects(ListKey::MEMBRES_SEARCH_RESULTS,$results);
                    break;
                case Mode::EXCLUDE_PREVIOUS: //exclude new results to the previous
                    $sessionContainer->removeObjects(ListKey::MEMBRES_SEARCH_RESULTS,$results);
                    break;
                case Mode::STANDARD:
                default:
                    $sessionContainer->setObjects(ListKey::MEMBRES_SEARCH_RESULTS,$results);

            }

        }

        return array('membreForm'=>$membreForm->createView(),'list_key'=>ListKey::MEMBRES_SEARCH_RESULTS);
    }




}
