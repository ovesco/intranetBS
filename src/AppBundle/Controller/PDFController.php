<?php

namespace AppBundle\Controller;

use AppBundle\Form\FamilleType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PDFController
 * @package AppBundle\Controller
 * @route("PDF/")
 */
class PDFController extends Controller {

    /**
     * Affiche la page qui permet de génerer des PDFs
     * @route("generator", name="pdf_generator")
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
     * @route("renderer", name="pdf_renderer")
     */
    public function PDFRendererAction() {

        $session    = $this->get('session');

        if(!$session->has('pdf_generator'))
            $session->set('pdf_generator', $this->get('pdf'));

        $pdf = $session->get('pdf_generator');

        $response   = new Response();
        $response->headers->set('content-type', 'application/pdf');
        $response->setContent($pdf->output());

        $session->set('pdf_generator', $pdf);

        return $response;

    }
}
