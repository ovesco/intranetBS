<?php

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Utils\Envoi\ListeEnvoi;


/**
 * Class EnvoiController
 * @package AppBundle\Controller
 *
 * @Route("/envoi")
 */
class EnvoiController extends Controller{


    /**
     * Genere la page principale d'action sur les envois
     *
     * @route("/liste", name="utils_envoi_liste")
     * @Template("Envoi/listeEnvoi.html.twig")
     * @return Response
     */
    public function listeAction()
    {
        $listeEnvoi = $this->get('listeEnvoi');

        return array('listeEnvoi'=>$listeEnvoi);
    }


    /**
     * @route("/clear", name="utils_envoi_clear")
     * @Template("Envoi/listeEnvoi.html.twig")
     * @return Response
     */
    public function clearAction()
    {
        $listeEnvoi = $this->get('listeEnvoi');
        $listeEnvoi->clearEnvois();

        return $this->redirect($this->generateUrl('utils_envoi_liste'));
    }

    /**
     * @param $token
     * @route("/remove_token/{token}", name="utils_envoi_remove_by_token")
     * @Template("Envoi/listeEnvoi.html.twig")
     * @return Response
     */
    public function removeByToken($token)
    {
        $listeEnvoi = $this->get('listeEnvoi');
        $listeEnvoi->removeEnvoiByTocken($token);
        $listeEnvoi->save();

        return $this->redirect($this->generateUrl('utils_envoi_liste'));
    }


    /**
     * @route("/print_pdf", name="utils_envoi_print_pdf")
     *
     * @return Pdf
     */
    public function printPdfAction()
    {
        $listeEnvoi = $this->get('listeEnvoi');
        $pdf = $this->get('Pdf'); //call service
        $pdf = $listeEnvoi->printPdfToCourrier($pdf);
        $listeEnvoi->save();

        return $pdf->Output('','I');
    }


}