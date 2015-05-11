<?php

namespace Interne\GalerieBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Interne\GalerieBundle\Entity\Album;
use Interne\GalerieBundle\Entity\Dossier;
use Interne\GalerieBundle\Form\AlbumType;
use Interne\GalerieBundle\Form\DossierType;
use Interne\GalerieBundle\Utils\Photo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Utils\Data\Sanitizer as Useful;
/**
 * Gère toutes les routes et actions internes, c'est-à-dire accessible uniquement aux personnes authentifiées
 * @package Interne\GalerieBundle\Controller
 */
class DossierAdminController extends Controller
{
    /**
     * @route("manage-dossier/{dossier}", name="interne_galerie_dossier_managing", options={"expose"=true})
     * @paramConverter("dossier", class="InterneGalerieBundle:Dossier")
     * @param Dossier $dossier
     * @return Response
     */
    public function manageDossierAction(Dossier $dossier) {

        $album      = new Album();
        $albumType  = $this->createForm(new AlbumType(), $album);

        return $this->render('InterneGalerieBundle:Interne:dossier_managing.html.twig', array(

            'dossier'   => $dossier,
            'albumForm' => $albumType->createView()
        ));
    }

    /**
     * @route("{dossier}/ajouter-album", name="interne_galerie_dossier_nouvel_album")
     */
    public function addAlbumAction(Dossier $dossier, Request $request) {

        $album = new Album();
        $type  = $this->createForm(new AlbumType(), $album);

        $type->handleRequest($request);

        if ($type->isValid()) {

            $album->setPhotos(array());
            $album->setDateCreation(new \Datetime());
            $album->setRepertoire(null);
            $dossier->addAlbum($album);

            $em = $this->getDoctrine()->getManager();
            $em->persist($dossier);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('interne_galerie_dossier_managing', array('dossier' => $dossier->getId())));
    }





    /**
     * @route("ajax-view/manage-album/{album}", name="interne_galerie_dossier_manage_album_view", options={"expose"=true})
     * @paramConverter("album", class="InterneGalerieBundle:Album")
     * @param Album $album
     * @return Response
     */
    public function ajaxManageAlbumAction(Album $album) {

        return $this->render('InterneGalerieBundle:Interne:ajax_view_manage_album.html.twig', array(

            'album' => $album
        ));
    }







    /**
     * Méthode appelée par dropzone pour réaliser l'upload des photos
     * @route("add-photos", name="interne_galerie_album_add_photos", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function addPhotosAction(Request $request) {

        $files   = $request->files;
        $em      = $this->getDoctrine()->getManager();
        $album   = $request->get('album-id');
        $album   = $em->getRepository('InterneGalerieBundle:Album')->find($album);
        $dossier = $album->getDossier();

        /*
         * Pour chaque image :
         * - On génère un objet photo qui en contiendra les informations
         * - On génère un thumbnail de largeur 300
         * - on upload
         * Les images sont uploadées dans repertoire/photos/image.jpg
         * Les thumbnails dans repertoire/thumbnails/image.jpg
         */
        foreach ($files as $uploadedFile) {

            /*
             * On génère le nouveau nom de l'image. Celui ci est composé de :
             * - l'id de son album
             * - un timestamp
             * - un nombre aléatoire (pour le multi upload)
             */
            $data = explode('.', $uploadedFile->getClientOriginalName());
            $ext  = $data[count($data)-1];
            $name = $album->getId() . '_' . time() . '_' . mt_rand(10,9999) . '.' . $ext;

            /*
             * On génère le répertoire si celui-ci n'existe pas encore
             */
            if($album->getRepertoire() == null)
                $album->setRepertoire(Useful::cleanString($dossier->getNom()) . '/' . date('Y') . '/' . Useful::cleanString($album->getNom()) . '/');


            //On génère un objet photo qui ira dans l'album
            $photo            = new Photo();
            $photo->size      = $uploadedFile->getClientSize();
            $photo->extension = $ext;
            $photo->nom       = $name;
            $photo->directory = $album->getRepertoire();

            /*
             * On réalise ensuite une série de tests pour déterminer si on upload le fichier ou pas. On va ainsi
             * vérifier la taille et l'extension du fichier
             * Taille maximale : 1048576 octets = 1 Mo
             * extensions acceptées : jpeg, jpg
             */
            if($photo->size > 1048576)
                return new Response("Le fichier est trop volumineux. Maximum : 1Mo", 500);
            if(!in_array($photo->extension, array('jpeg', 'jpg')))
                return new Response("L'extension du fichier n'est pas compatible. Acceptés : jpg, jpeg. Reçu : " . $photo->extension, 500);


            //On upload le fichier
            $file = $uploadedFile->move($photo->getUploadRootDir() . 'photos/', $name);

            if($uploadedFile->getError() != 'UPLOADERROK')
                return new Response("Erreur lors de l'upload, code erreur : " . $uploadedFile->getError(), 500);

            /*
             * On génère ensuite le thumbnail
             */
            $thumbPath      = $photo->getUploadRootDir() . 'thumbnails/';

            if (!file_exists($thumbPath)) {
                mkdir($thumbPath, 0777, true);
            }

            $desired_height = 120;
            $source_image   = imagecreatefromjpeg($photo->getUploadRootDir() . 'photos/' . $name);
            $width          = imagesx($source_image);
            $height         = imagesy($source_image);
            $desired_width  = floor($width * ($desired_height / $height));
            $virtual_image  = imagecreatetruecolor($desired_width, $desired_height);
            imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
            imagejpeg($virtual_image, $thumbPath . $name);

            //On ajoute ensuite la photo à l'album
            $album->addPhoto($photo);
        }

        $em->persist($album);
        $em->flush();

        return new Response();
    }

    /**
     * Supprimme un album entier
     * @route("remove-album/{album}", name="interne_galerie_dossier_supprimer_album")
     * @paramConverter("album", class="InterneGalerieBundle:Album")
     * @param Album $album
     * @return Response
     */
    public function removeAlbumAction(Album $album) {

        $dossier = $album->getDossier();
        $dossier->removeAlbum($album);

        //On supprimme ensuite le répértoire du système
        $dir = __DIR__ . '/../../../../web/galerie/photos/' . $album->getRepertoire();

        try {
            $this->deleteDirectory($dir);
        } catch(\Exception $e) {

        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($album);
        $em->persist($dossier);
        $em->flush();

        return $this->redirect($this->generateUrl('interne_galerie_dossier_managing', array('dossier' => $dossier->getId())));
    }


    /**
     * Supprimme un répértoire de la galerie
     * @param $directory
     */
    public function deleteDirectory($directory) {
        $dir = opendir($directory);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($directory . '/' . $file) )
                    rmdir($directory . '/' . $file);

                else
                    unlink($directory . '/' . $file);

            }
        }
        rmdir($directory);
        closedir($dir);
    }

}
