<?php

namespace AppBundle\Controller;

use AppBundle\Form\FamilleType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PDFController
 * @package AppBundle\Controller
 * @route("PDF/generator/")
 */
class PDFController extends Controller {

    /**
     * Affiche la page qui permet de génerer des PDFs
     * @route("", name="pdf_generator")
     * @template("PDF/generator.html.twig")
     */
    public function PDFGeneratorAction() {

        $listing        = $this->get('listing');
        $user           = $this->getDoctrine()->getManager()->getRepository('AppBundle:Membre')->find(3);
        $attribution    = $user->getActiveAttribution();

        return array(
            'listing'       => $listing,
            'attribution'   => $attribution
        );
    }

    /**
     * Cette méthode génère un fichier PDF à partir des données choisies par l'utilisateur. Le PDF est donc mis à jour
     * de manière dynamique. Pour permettre cela, le PDF est stocké en session, et actualisé au fur et à mesure
     * @route("renderer", name="pdf_renderer", options={"expose"=true})
     */
    public function PDFRendererAction() {

        $session    = $this->get('session');

        /*
         * Mise en place des paramètres du PDF
         * Ceux-ci sont stockés en session, et ne changent pas forcement (dans le cas d'un ajout de template par exemple)
         */
        $pdf        = $this->get('pdf');
        $params     = ($session->has('pdf-generator')) ? $session->get('pdf-generator') : $this->getDefaultParameters();










        /*
         * Chargement du template
         *
         * Les templates sont stockés dans /web/temporary/pdf_templates et portent un nom aléatoire gardé uniquement
         * le temps de la session. Les fichiers ne sont pas supprimés, de manière à pouvoir éventuellement les récupérer.
         * Le répertoire temporary peut-être vidé par un administrateur
         */
        if($params['template'] != null)
            $pdf->loadTemplate(getcwd() . '/temporary/pdf_templates/' . $params['template'], $params['template_height']);







        /*
         * Chargement de la liste de membres
         * La liste peut soit provenir du listing, dans ce cas elle est identifiée par son token
         * soit être une liste liée à un groupe, dans ce cas on regarde quel groupe, et on récupère la liste des membres
         * de manière recursive
         */
        $membres    = $this->queryListe($params['liste']);

        $data       = array();

        foreach($membres as $m)
            $data[] = array($m->getPrenom(), $m->getNom(), $m->getNaissance()->format('d.m.Y'));

        $pdf->printData(array('prenom', 'nom', 'naissance'), array(5,8,4), $data);




        /*
         * On charge le modèle
         */



        /*
         * Renvoi du PDF pour affichage
         */
        $response   = new Response();
        $response->headers->set('content-type', 'application/pdf');
        $response->setContent($pdf->output());

        return $response;

    }

    /**
     * @route("load-template", name="pdf_load_template", options={"expose"=true})
     */
    public function loadPDFTemplateAction(Request $request) {

        $upFile = $request->files->get('template');
        $path   = getcwd() . '/temporary/pdf_templates/';
        $name   = time() . rand(1,100) . '.pdf';
        $file   = $upFile->move($path, $name);

        $pdfData             = $this->get('session')->get('pdf-generator'); //Le service pour génerer des PDFs

        $pdfData['template'] = $name;
        $this->get('session')->set('pdf-generator', $pdfData);


        return new JsonResponse('');
    }

    /**
     * @route("pdf-params-update", name="pdf_generator_update", options={"expose"=true})
     */
    public function paramsUpdateAction(Request $request) {

        $current    = $this->get('session')->get('pdf-generator');
        $base       = $request->get('base');
        $perso      = $request->get('subParams');

        if($base['type'] != '')
            $current['type'] = $base['type'];

        if($base['liste'] != '')
            $current['liste'] = $base['liste'];

        foreach($perso as $k => $v)
            $current['fields'][$k] = $v;

        $this->get('session')->set('pdf-generator', $current);


        return new Response('');
    }

    /**
     * @route("pdf-generator-reinitialise", name="pdf_generator_reinitialise", options={"expose"=true})
     */
    public function reinitialiseAction() {

        $this->get('session')->set('pdf-generator', $this->getDefaultParameters());
    }

    private function getDefaultParameters() {

        return array(

            'type'              => 'base',
            'liste'             => null,
            'template'          => null,
            'template_height'   => 30,
            'fields'            => array('nom', 'prenom', 'adresse', 'telephone')
        );
    }

    private function queryListe($liste) {

        $membres = null;
        $params  = explode('__', $liste);

        if(count($params) == 2)
            $membres = $this->getDoctrine()->getRepository('AppBundle:Groupe')->find($params[1])->getMembersRecursive();

        else $membres = $this->get('listing')->getByToken($liste);

        return $membres;
    }
}
