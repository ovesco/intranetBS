<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Famille;
use AppBundle\Entity\Membre;
use AppBundle\Form\AddMembreType;
use AppBundle\Form\VoirMembreType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/* annotations */
use AppBundle\Utils\Menu\Menu;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


/**
 * Class MembreController
 * @package AppBundle\Controller
 * @Route("/membre")
 */
class MembreController extends Controller {

    /**
     * Affiche la page d'ajout de membre -> Membre/page_ajouter_membre.html.twig
     * et valide le formulaire si celui-ci est soumis
     * @Route("/ajouter", name="interne_ajouter_membre")
     * @Menu("Ajouter un membre",block="database",order=1, icon="add", expanded=true)
     * @param Request $request
     * @return Response
     */
    public function ajouterMembreAction(Request $request) {

        $membre             = new Membre();
        $membreForm         = $this->createForm(new AddMembreType, $membre);

        return $this->render('AppBundle:Membre:page_ajouter_membre.html.twig', array(
            'membreForm'        => $membreForm->createView(),
        ));


        /*
        $membre             = new Membre();
        $famille            = new Famille();
        $membreForm         = $this->createForm(new AddMembreType, $membre);
        $membreFamilleForm  = $this->createForm(new MembreFamilleType(), $membre);
        $familleForm        = $this->createForm(new FamilleType, $famille);
        $em                 = $this->getDoctrine()->getManager();


        /*
         * La grosse galère pour valider de manière propre.
         * Fonctionnement :
         * - en premier lieu, la famille du membre est un formulaire différent (membreFamilleForm) qui contient
         *   seulement le champ famille
         * - Ensuite on traite d'abord le formulaire du membre n' sh*t
         * - Ensuite, si la valeur de membreFamille était NOEXIST (aucune famille), alors on valide le formulaire de famille
         * - Sinon, on récupère la bonne famille (id transmise par membreFamille)
         *
        $membreForm->handleRequest($request);

        if ($membreForm->isValid()) {

            $membre->setNaissance(new \Datetime($membre->getNaissance()));
            $membre->setValidity(0);

            if($membre->getAdresse()->getRue() == null) $membre->setAdresse(null);


            if($request->request->get('membre_famille')['famille'] == 'NOEXIST') {

                $familleForm->handleRequest($request);

                if ($familleForm->isValid()) {

                    $famille->setValidity(0);
                    $famille->getPere()->setSexe(Personne::HOMME);
                    $famille->getMere()->setSexe(Personne::FEMME);

                    /*
                     * On analyse les informations sur les geniteurs, pour savoir si on les set à null,
                     * ainsi que les informations sur les adresses
                     *
                    if($famille->getAdresse()->getRue() == null) $famille->setAdresse(null);
                    if($famille->getPere()->getPrenom() == null) $famille->setPere(null);
                    if($famille->getMere()->getPrenom() == null) $famille->setMere(null);
                    if($famille->getPere() != null && $famille->getPere()->getAdresse()->getRue() == null) $famille->getPere()->setAdresse(null);
                    if($famille->getMere() != null && $famille->getMere()->getAdresse()->getRue() == null) $famille->getMere()->setAdresse(null);

                    if($famille->getAdresse() == null && $famille->getMere()->getAdresse() == null && $famille->getPere()->getAdresse() == null)
                        throw new \Exception("Il ne peut pas y avoir AUCUNE adresse pour la famille");
                }
            }
            else
                $famille = $em->getRepository('AppBundle:Famille')->find($request->request->get('membre_famille')['famille']);

            $famille->addMembre($membre);
            $em->persist($famille);

            /*
             * On ajoute un user de manière automatique
             * au membre nouvellement créé
             *
            $user = new User();
            $user->setUsername(clearer::cleanNames($membre->getPrenom()) . "." . $membre->getNom());
            $user->setPassword($membre->getNom() . time());
            $user->setMembre($membre);

            $em->flush();

            $em->persist($user);

            $em->flush(); // flush après que le membre soit persisté de manière à pas avoir d'erreurs

            return $this->redirect($this->generateUrl('interne_voir_membre', array('membre' => $membre->getId())));


        }

        return $this->render('AppBundle:Membre:page_ajouter_membre.html.twig', array(

            'membreForm'        => $membreForm->createView(),
            'membreFamilleForm' => $membreFamilleForm->createView(),
            'familleForm'       => $familleForm->createView(),
        ));

        */


    }




    /**
     * Cette fonction retourne une proprieté d'un membre donné par son id. la proprieté doit être du type param1__param2...
     * (getFamille()->getAdresse())
     * @param $membre Membre le membre
     * @param $property la proprieté à atteindre
     * @return mixed proprieté
     *
     * @Route("ajax/get-property/{membre}/{property}", name="interne_ajax_membre_get_property", options={"expose"=true})
     * @ParamConverter("membre", class="AppBundle:Membre")
     */
    public function getMembrePropertyAction(Membre $membre, $property) {

        $accessor = $this->get('accessor');
        $serializer = $this->get('jms_serializer');

        $data = $serializer->serialize($accessor->getProperty($membre, $property), 'json');
        return new JsonResponse($data);
    }



    /**
     * @Route("/voir/{membre}", name="interne_voir_membre", requirements={"membre" = "\d+"})
     * @ParamConverter("membre", class="AppBundle:Membre")
     */
    public function voirMembreAction($membre) {

        $membreForm = $this->createForm(new VoirMembreType(), $membre);

        return $this->render('AppBundle:Membre:page_voir_membre.html.twig', array(

                'membre'            => $membre,
                'listing'           => $this->get('listing'),
                'membreForm'        => $membreForm->createView(),
            )
        );
    }


    /**
     * @Route("/voir/{membre}/pdf", name="membre_voir_pdf", requirements={"membre" = "\d+"})
     * @ParamConverter("membre", class="AppBundle:Membre")
     */
    public function voirMembrePdfAction($membre)
    {

        $membreForm = $this->createForm(new VoirMembreType(), $membre);

        $html = $this->render('AppBundle:Membre:pdf_voir_membre.html.twig', array(

                'membre' => $membre,
                'listing' => $this->get('listing'),
                'membreForm' => $membreForm->createView(),
            )
        );

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="file.pdf"'
            )
        );

    }


    /**
     * @param $membre membre le membre
     * @param $type string 'attribution' ou 'distinction'
     * @param $obj int l'id de l'attribution ou distinction
     * @return jsonresponse
     * @Route("/ajax/remove-attribution-or-distinction/{membre}/{type}/{obj}", name="interne_ajax_membre_remove_attr_dist", options={"expose"=true})
     * @ParamConverter("membre", class="AppBundle:Membre")
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
     * @Route("/ajax/verify-numero-bs/{numero}", name="interne_membre_ajax_verify_numero_bs", options={"expose"=true}, requirements={"numero" = "\d+"})
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
     * @Route("/ajax/modify-famille/{membre}/{famille}", name="membre_modify_famille", options={"expose"=true})
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
}
