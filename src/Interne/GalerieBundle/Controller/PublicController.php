<?php

namespace Interne\GalerieBundle\Controller;

use Interne\GalerieBundle\Entity\Album;
use Interne\GalerieBundle\Entity\Dossier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PublicController
 * Gère toutes les routes et actions publiques, c'est-à-dire accessible sans être authentifié
 * @package Interne\GalerieBundle\Controller
 */
class PublicController extends Controller
{
    /**
     * @Route("", name="public_galerie_accueil")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function accueilGalerieAction()
    {
        //On récupère la liste des derniers albums ajoutés
        $em = $this->getDoctrine()->getManager();

        $query    = $em->createQuery('SELECT a FROM InterneGalerieBundle:Album a');
        $albums   = $query->setMaxResults(4)->getResult();

        $dossiers = $em->getRepository('InterneGalerieBundle:Dossier')->findByParent(null);


        return $this->render('InterneGalerieBundle:Public:index.html.twig', array(

            'derniersAlbums' => $albums,
            'dossiers'       => $dossiers
        ));
    }

    /**
     * @Route("dossier/{dossier}", name="public_galerie_voir_dossier")
     * @param Dossier $dossier
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewDossierAction(Dossier $dossier) {

        //On teste en premier lieu si le dossier est accessible ou pas
        if($dossier->getFullLocked()) return $this->render('InterneGalerieBundle:Public:locked.html.twig');

        //On génère la hierarchie pour le breadcrumb
        $hierarchie = array();
        $current    = $dossier->getParent();

        while($current != null){

            $hierarchie[] = $current;
            $current      = $current->getParent();
        }

        $hierarchie = array_reverse($hierarchie);



        return $this->render('InterneGalerieBundle:Public:voir_dossier.html.twig', array(

            'dossier'       => $dossier,
            'hierarchie'    => $hierarchie
        ));
    }

    /**
     * @Route("album/{album}", name="public_galerie_voir_album")
     * @param Album $album
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAlbumAction(Album $album) {

        //On teste en premier lieu si l'album est accessible ou pas
        if($album->getDossier()->getFullLocked()) return $this->render('InterneGalerieBundle:Public:locked.html.twig');

        return $this->render('InterneGalerieBundle:Public:voir_album.html.twig', array(

            'album'     => $album
        ));
    }
}
