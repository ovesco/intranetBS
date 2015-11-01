<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Attribution;
use AppBundle\Entity\Membre;
use AppBundle\Form\AttributionType;
use Doctrine\ORM\EntityManager;
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
     * @Route("/modal-or-persist", name="interne_attribution_render_modale_or_persist")
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

            /** @var EntityManager $em */
            $em         = $this->getDoctrine()->getManager();
            $membreRepo = $em->getRepository('AppBundle:Membre');
            $fnRepo     = $em->getRepository('AppBundle:Fonction');
            $grpRepo    = $em->getRepository('AppBundle:Groupe');
            $transfo    = new DateTimeToStringTransformer(null, null, 'd.m.Y');

            $parameters = $request->request->getIterator();
            $data       = array();

            /*
             * On met en place un iterateur
             * On va ensuite iterer sur chaque attributionData jusqu'à toutes les avoir parcourues. On les range ainsi dans un
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
            foreach ($data as $id => $attributionData) {


                // On fait les tests de validité des données
                if (!isset($attributionData['debut']) || $attributionData['debut'] == "")
                    continue;
                if (!isset($attributionData['fonction']) || !is_numeric($attributionData['fonction']))
                    continue;
                if (!isset($attributionData['groupe']) || !is_numeric($attributionData['groupe']))
                    continue;

                $attribution = new Attribution();
                $attribution->setDateDebut($transfo->reverseTransform($attributionData['debut']));
                $attribution->setDateFin($transfo->reverseTransform($attributionData['fin']));
                $attribution->setFonction($fnRepo->find($attributionData['fonction']));
                $attribution->setGroupe($grpRepo->find($attributionData['groupe']));
                $attribution->setMembre($membreRepo->find($id));

                $em->persist($attribution);
            }

            $em->flush();

            return $this->redirect( $request->headers->get('referer') );

        }

        return $this->render('AppBundle:Attribution:modal_add_attribution.html.twig');
    }


    /**
     * @Route("/render-form", name="interne_attribution_render_formulaire_modal", options={"expose"=true});
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

        return $this->render('AppBundle:Attribution:partial_add_attribution_form.html.twig', array(

            'membres'   => $mem,
            'form'      => $this->createForm(new AttributionType(), new Attribution())->createView()
        ));
    }


    /**
     * Appelée pour terminer une chiée d'attributions en même temps
     *
     * @Route("/terminer-attributions", name="interne_attribution_terminer")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function terminateAttributionsAction(Request $request) {

        $ids = explode(",", $request->get('ids'));
        $fin = $request->get('dateFin');

        /** @var EntityManager $em */
        $em  = $this->getDoctrine()->getManager();
        $rep = $em->getRepository('AppBundle:Attribution');
        $tr  = new DateTimeToStringTransformer(null, null, 'd.m.Y');

        foreach($ids as $id) {
            $attr = $rep->find($id)->setDateFin( $tr->reverseTransform($fin) );
            $em->persist($attr);
        }

        $em->flush();

        return $this->redirect( $request->headers->get('referer') );
    }

    /**
     * Supprimme une attribution
     * @Route("/remove/{attribution}", name="attribution_delete", options={"expose"=true})
     * @ParamConverter("attribution", class="AppBundle:Attribution")
     * @param $attribution
     * @return JsonResponse
     */
    public function removeAttributionAction(Attribution $attribution)
    {

        $em = $this->getDoctrine()->getManager();

        $em->remove($attribution);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


    /**
     * @Route("/modal/add", name="attribution_add_modal", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function addAttributionFormAjaxAction(Request $request)
    {
        $attribution = new Attribution();

        $attributionForm = $this->createForm(new AttributionType(), $attribution, array(
            'action' => $this->generateUrl('attribution_add')
        ));

        return $this->render('AppBundle:Attribution:attribution_form_modal.html.twig', array(
                'form' => $attributionForm->createView(),
                'postform' => $attributionForm)
        );
    }


    /**
     * @Route("/modal/add/{membre}", name="attribution_add_modal", options={"expose"=true})
     *
     * @param Request $request
     * @param Membre $membre
     * @ParamConverter("membre", class="AppBundle:Membre")
     * @return Response
     */
    public function addAttributionWithMembreFormAjaxAction(Request $request, Membre $membre)
    {
        $attribution = new Attribution();

        /* Formulaire multimembre
        $attributionForm = $this->createForm(new AttributionMultiMembreType(), $attribution, array(
            'action' => $this->generateUrl('attribution_add_multimembre'),
            'attr' => array(
                'membres' => implode(",", $idMembre)
            )
        ));
        */

        $attribution->setMembre($membre);

        $attributionForm = $this->createForm(new AttributionType(), $attribution, array(
            'action' => $this->generateUrl('attribution_add')
        ));

        return $this->render('AppBundle:Attribution:attribution_form_modal.html.twig', array(
                'form' => $attributionForm->createView())
        );
    }


    /**
     * @Route("/modal/edit/{attribution}", name="attribution_edit_modal", options={"expose"=true})
     *
     * @param Attribution $attribution
     * @ParamConverter("attribution", class="AppBundle:Attribution")
     * @return Response
     */
    public function editAttributionFormAjaxAction($attribution)
    {
        $attributionForm = $this->createForm(new AttributionType(), $attribution,
            array('action' => $this->generateUrl('attribution_edit', array('attribution' => $attribution->getId()))));

        return $this->render('AppBundle:Attribution:attribution_form_modal.html.twig', array(
                'form' => $attributionForm->createView(),
                'postform' => $attributionForm)
        );
    }

    /**
     * @Route("/modal/terminate/{attribution}/{dateFin}", name="attribution_terminate", options={"expose"=true})
     *
     * @param Attribution $attribution
     * @param \DateTime $dateFin
     * @ParamConverter("attribution", class="AppBundle:Attribution")
     * @ParamConverter("dateFin", class="\DateTime")
     * @return JsonResponse
     */
    public function terminateAttribution($attribution, $dateFin)
    {
        if ($dateFin < $attribution->getDateDebut())
            return new JsonResponse("L'attribution doit se terminer APRES avoir débuté (c'est logique, tu t'es gourré)", Response::HTTP_BAD_REQUEST);

        $attribution->setDateFin($dateFin);

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $em->persist($attribution);

        return new JsonResponse($attribution, Response::HTTP_OK);
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

            return new JsonResponse($newAttribution, Response::HTTP_CREATED);
        }

        return $this->render('AppBundle:Attribution:attribution_form_modal.html.twig', array(
            'form' => $newAttributionForm->createView(),
            'postform' => $newAttributionForm));
    }

    /**
     * @Route("/add-multimembre", name="attribution_add_multimembre", options={"expose"=true})
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

            return new JsonResponse($newAttribution, Response::HTTP_CREATED);
        }

        return $this->render('AppBundle:Attribution:attribution_form_modal.html.twig', array(
            'form' => $newAttributionForm->createView(),
                'postform' => $newAttributionForm)
        );
    }

    /**
     * @Route("/edit/{attribution}", name="attribution_edit", options={"expose"=true})
     *
     * @param Request $request
     * @param Attribution $attribution
     * @return Response
     * @ParamConverter("attribution", class="AppBundle:Attribution")
     */
    public function editAttribution(Request $request, Attribution $attribution)
    {
        $attributionForm = $this->createForm(new AttributionType(), $attribution);
        $attributionForm->handleRequest($request);

        if ($attributionForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($attribution);
            $em->flush();

            return new JsonResponse($attribution, Response::HTTP_OK);
        }

        return new JsonResponse("Le formulaire contient une erreur", Response::HTTP_BAD_REQUEST);
    }
}
?>
