<?php

namespace AppBundle\Controller;

use AppBundle\Form\FamilleType;
use AppBundle\Utils\DataValidation\Themer;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Membre;
use AppBundle\Entity\Famille;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FamilleController
 * @package AppBundle\Controller
 * @route("/famille")
 */
class FamilleController extends Controller {


    /**
     * @param $famille Famille la famille
     * @return Response la vue
     *
     * @paramConverter("famille", class="AppBundle:Famille")
     * @route("/voir/{famille}", name="interne_voir_famille")
     * @Template("AppBundle:Famille:page_voir_famille.html.twig")
     */
    public function showFamilleAction($famille) {

        $familleForm = $this->createForm(new FamilleType, $famille);

        return array(
            'listing'       => $this->get('listing'),
            'famille'       => $famille,
            'familleForm'   => $familleForm->createView()
        );
    }

    /**
     * Cette fonction retourne une proprieté d'une famille donnés par son id. la proprieté doit être du type param1__param2...
     * (getPere()->getAdresse())
     * @param $famille Famille la famille
     * @param $property la proprieté à atteindre
     * @return mixed proprieté
     *
     * @route("/ajax/get-property/{famille}/{property}", name="interne_ajax_famille_get_property", options={"expose"=true})
     * @ParamConverter("famille", class="AppBundle:Famille")
     */
    public function getFamillePropertyAction(Famille $famille, $property) {

        $accessor   = $this->get('accessor');
        $serializer = $this->get('jms_serializer');

        return new JsonResponse($serializer->serialize($accessor->getProperty($famille, $property), 'json'));
    }


    /**
     * Retourne la liste de toutes les familles disponibles sous la forme d'un objet JSON
     * @route("/ajax/get-familles-as-json", name="interne_ajax_get_familles_as_json", options={"expose"=true})
     */
    public function getFamillesAsJson() {

        $familles   = $this->getDoctrine()->getRepository('AppBundle:Famille')->findAll();
        $return     = array();

        foreach($familles as $k => $f) {

            $return[$k] = array(
                'id' => $f->getId(),
                'nom' => $f->getNom()
            );
        }

        return new JsonResponse($return);
    }



    /**
     * Cette méthode est appelée dans le cadre de l'ajout d'un membre, pour récupérer toutes les éventuelles adresses déjà enregistrées
     * pour la famille (Géniteurs y compris)
     * @param $famille Famille la famille
     * @return mixed proprieté
     *
     * @route("ajax/get-adresses/{famille}", name="interne_ajax_famille_get_adresses", options={"expose"=true})
     * @ParamConverter("famille", class="AppBundle:Famille")
     */
    public function getFamilleAdressesAction(Famille $famille) {

        $accessor   = $this->get('accessor');
        $serializer = $this->get('jms_serializer');

        $adresses   = array(

            'famille'   => $serializer->serialize($accessor->getProperty($famille, 'adresse'), 'json'),
            'pere'      => $serializer->serialize($accessor->getProperty($famille, 'pere.adresse'), 'json'),
            'mere'      => $serializer->serialize($accessor->getProperty($famille, 'mere.adresse'), 'json')
        );

        return new JsonResponse($adresses);
    }
} 