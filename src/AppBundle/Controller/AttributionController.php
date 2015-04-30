<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Membre;
use AppBundle\Entity\Attribution;
use AppBundle\Form\AttributionType;
use AppBundle\Form\AttributionMultiMembreType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AttributionController
 * @package AppBundle\Controller
 *
 * @Route("/attribution")
 */
class AttributionController extends Controller
{
    /**
     * @param Request $request
     * @route("/modal-or-persist", name="interne_attribution_render_modale_or_persist")
     * Pour ajouter des attributions, c'est un peu plus compliqué que de simplement afficher le formulaire.
     * En effet on peut être tenté d'ajouter plusieurs attributions. Pour ce fait, on génère un formulaire dynamique
     * suivant le nombre de personnes qui ont besoin d'une attribution
     * @return Response
     */
    public function renderModalOrPersistAction(Request $request) {

        /*
         * Formulaire soumis, on ajoute une attribution pour chaque champ
         */
        if($request->get('is-submitted') == "oui") {

            $em         = $this->getDoctrine()->getManager();
            $membreRepo = $em->getRepository('AppBundle:Membre');
            $fnRepo     = $em->getRepository('AppBundle:Fonction');
            $grpRepo    = $em->getRepository('AppBundle:Groupe');
            $transfo    = new DateTimeToStringTransformer(null, null, 'd.m.Y');

            $parameters = $request->request->getIterator();
            $data       = array();

            /*
             * On met en place un iterateur
             * On va ensuite iterer sur chaque valeur jusqu'à toutes les avoir parcourues. On les range ainsi dans un
             * array
             */
            foreach($parameters as $k => $r) {

                $infos = explode('__', $k);

                if($infos[0] == "fonction")
                    $data[ $infos[1] ]['fonction'] = $r;

                else if($infos[0] == "groupe")
                    $data[ $infos[1] ]['groupe'] = $r;

                else if($infos[0] == "date-debut")
                    $data[ $infos[1] ]['debut'] = $r;

                else if($infos[0] == "date-fin")
                    $data[ $infos[1] ]['fin'] = $r;
            }

            /*
             * Ensuite on commence à génerer des attributions à la pelle pour tous les membres concernés
             * On utilise les data-transformers pour les dates, et les $em directement pour le reste
             */
            foreach($data as $id => $yolo) {

                if(
                    !( isset($yolo['debut']) && isset($yolo['fin']) && isset($yolo['fonction']) && isset($yolo['groupe']))
                    || !(is_null($yolo['debut']) && is_null($yolo['fin']) && is_null($yolo['fonction']) && is_null($yolo['groupe']))
                    || !($yolo['debut'] == "" && $yolo['fin'] == "" && $yolo['fonction'] == "" && $yolo['groupe'] == ""))
                    continue;

                $attribution = new Attribution();
                $attribution->setDateDebut( $transfo->reverseTransform($yolo['debut']) );
                $attribution->setDateFin( $transfo->reverseTransform($yolo['fin']) );
                $attribution->setFonction($fnRepo->find($yolo['fonction']));
                $attribution->setGroupe($grpRepo->find($yolo['groupe']));
                $attribution->setMembre($membreRepo->find($id));

                $em->persist($attribution);
            }

            $em->flush();

            return $this->redirect( $request->headers->get('referer') );

        }

        return $this->render('AppBundle:Modales:modal_add_attribution.html.twig');
    }


    /**
     * @route("render-form", name="interne_attribution_render_formulaire_modal", options={"expose"=true});
     * @param Request $request
     * @return Response
     */
    public function renderFormAction(Request $request) {

        $ids = $request->get('membres');
        $em  = $this->getDoctrine()->getManager();
        $rep = $em->getRepository('AppBundle:Membre');
        $mem = array();

        foreach($ids as $id)
            $mem[] = $rep->find($id);

        return $this->render('AppBundle:Partials:partial_add_attribution_form.html.twig', array(

            'membres'   => $mem,
            'form'      => $this->createForm(new AttributionType(), new Attribution())->createView()
        ));
    }


    /**
     * Appelée pour terminer une chiée d'attributions en même temps
     * @route("terminer-attributions", name="interne_attribution_terminer")
     */
    public function terminateAttributionsAction(Request $request) {

        $ids = $request->get('ids');
        $fin = $request->get('dateFin');


    }




































    /**
     * @Route("/get-modal", name="attribution_get_modal", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     */
    public function getAttributionFormAjaxAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        /*
         * On envoie le formulaire en modal
         */

        $idMembre = $request->request->get('idMembre');
        $idAttribution = $request->request->get('idAttribution');

        $attribution = null;
        $attributionForm = null;
        if ($idAttribution == null) {
            /*
             * Ajout
             */
            $attribution = new Attribution();

            /* S'il y a des données de membres renseignées */
            if($idMembre !== null) {

                /* Tester s'il y en a plusieurs */
                if( is_array($idMembre) ) {

                    /* Formulaire multimembre */
                    $attributionForm = $this->createForm(new AttributionMultiMembreType(), $attribution, array(
                        'action'    => $this->generateUrl('attribution_multimembre_add'),
                        'attr'      => array(
                            'membres'    => implode(",", $idMembre)
                        )
                    ));

                } else {
                    /* Formulaire simple */
                    $attribution->setMembre($em->getRepository('AppBundle:Membre')->find($idMembre));
                }
            }

            /* S'il n'y a pas de données, mettre le formulaire simple */
            if($attributionForm === null) {
                $attributionForm = $this->createForm(new AttributionType(), $attribution, array(
                    'action' => $this->generateUrl('attribution_add')
                ));
            }

        } else {
            /*
             * Modification
             */
            //TODO: pas testé
            $attribution = $em->getRepository('AppBundle:Attribution')->find($idAttribution);
            $attributionForm = $this->createForm(new AttributionType(), $attribution,
                array('action' => $this->generateUrl('attribution_edit')));

        }

        return $this->render('AppBundle:Attribution:attribution_form_modal.html.twig', array(
            'form' => $attributionForm->createView(),
            'postform' => $attributionForm)
        );

    }


    /**
     * @Route("/add", name="attribution_add", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     */
    public function addAttributionAction(Request $request)
    {

        $newAttribution = new Attribution();
        $newAttributionForm = $this->createForm(new AttributionType(), $newAttribution);

        $newAttributionForm->handleRequest($request);

        if($newAttributionForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newAttribution);
            $em->flush();

            return new JsonResponse(true);
        }

        return $this->render('AppBundle:Attribution:attribution_form_modal.html.twig', array(
            'form' => $newAttributionForm->createView(),
            'postform' => $newAttributionForm));
    }

    /**
     * @Route("/add-multimembre", name="attribution_multimembre_add", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     */
    public function addAttributionMultiMembreAction(Request $request)
    {

        $newAttribution = new Attribution();
        $newAttributionForm = $this->createForm(new AttributionMultiMembreType(), $newAttribution);

        $newAttributionForm->handleRequest($request);

        if($newAttributionForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newAttribution);
            $em->flush();

            return new JsonResponse(true);
        }

        return $this->render('AppBundle:Attribution:attribution_form_modal.html.twig', array(
            'form' => $newAttributionForm->createView(),
            'postform' => $newAttributionForm));
    }

    /**
     * @Route("/edit/{attribution}", name="attribution_edit", options={"expose"=true})
     *
     * @param Request $request
     * @param Attribution $attribution
     * @return Response
     * @ParamConverter("attribution", class="AppBundle:Attribution")
     */
    public function editAttribution(Attribution $attribution, Request $request)
    {
        //TODO: modifier une attribution (ou peut-être ne veut-on que les supprimer ?)
    }

    //TODO : remove
}

?>