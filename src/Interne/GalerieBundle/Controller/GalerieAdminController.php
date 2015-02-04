<?php

namespace Interne\GalerieBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Interne\GalerieBundle\Entity\Dossier;
use Interne\GalerieBundle\Form\DossierType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


/**
 * Gère toutes les routes et actions internes, c'est-à-dire accessible uniquement aux personnes authentifiées
 * @package Interne\GalerieBundle\Controller
 */
class GalerieAdminController extends Controller
{
    /**
     * @route("global-managing", name="galerie_interne_global_managing")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function globalManagingAction()
    {
        $dossier     = new Dossier();
        $dossierType = $this->createForm(new DossierType(), $dossier);
        $em          = $this->getDoctrine()->getManager();

        return $this->render('InterneGalerieBundle:Interne:global_managing.html.twig', array(

            'Hdossiers'   => $em->getRepository('InterneGalerieBundle:Dossier')->findByParent(null),
            'dossierForm' => $dossierType->createView(),
            'dossiers'    => $em->getRepository('InterneGalerieBundle:Dossier')->findAll(),
            'groupes'     => $em->getRepository('AppBundle:Groupe')->findAll()
        ));
    }





    /**
     * Permet de supprimer une image par son nom
     * Renvoie ensuite une redirection vers la page de gestion du dossier si la page était atteinte sans ajax
     * @param string $picture
     * @return Response
     * @route("image/remove/{picture}", name="interne_galerie_remove_picture", options={"expose"=true})
     */
    public function removeImageAction($picture) {

        $em     = $this->getDoctrine()->getManager();
        $data   = explode('_', $picture);

        $album  = $em->getRepository('InterneGalerieBundle:Album')->find($data[0]);
        $photos = new ArrayCollection($album->getPhotos());
        $photoa = $photos->filter(function($p) use ($picture) {

            if($p->nom == $picture) return $p;
        });

        $photo = null;

        foreach($photoa as $ph) //On target la bonne photo
            $photo = $ph;




        //On supprimme la photo
        $fs = new Filesystem();
        $fs->remove($photo->getAbsolutePath());

        //On la retire de l'album
        $photos->removeElement($photo);

        $album->setPhotos($photos->toArray());
        $em->persist($album);
        $em->flush();

        return $this->redirect($this->generateUrl('interne_galerie_dossier_managing', array('dossier' => $album->getDossier()->getId())));

    }


    /**
     * @route("dossier/ajouter", name="galerie_interne_ajouter_dossier")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajouterDossierAction(Request $request) {

        $dossier = new Dossier();
        $type    = $this->createForm(new DossierType(), $dossier);
        $em      = $this->getDoctrine()->getManager();


        //Test si on a lié un groupe ou pas
        if($request->request->get('interne_galeriebundle_dossier')['groupe'] == '0') {

            $data = $request->request->get('interne_galeriebundle_dossier');
            $data['groupe'] = null;

            $request->request->set('interne_galeriebundle_dossier', $data);
        }

        $type->handleRequest($request);

        if ($type->isValid()) {

            //On ajoute un éventuel groupe parent
            $parent = null;
            if($request->request->get('add-dossier-parent-id') != '')
                $parent = $em->getRepository('InterneGalerieBundle:Dossier')->find($request->request->get('add-dossier-parent-id'));

            $dossier->setParent($parent);
            $dossier->setLocked(false);
            $em->persist($dossier);
            $em->flush();

            return $this->redirect($this->generateUrl('galerie_interne_global_managing'));
        }

        else {
            /** @var Session $session */
            $session = $this->get('session');
            $session->getFlashBag()->add('error', "Il y a eu une erreur lors de l'ajout du dossier, veuillez réessayer");
            return $this->redirect($this->generateUrl('galerie_interne_global_managing'));
        }
    }







    /**
     * @route("dossier/manage-lock/{lock}/{dossier}", name="galerie_interne_manage_lock_dossier", options={"expose"=true})
     * @paramConverter("dossier", class="InterneGalerieBundle:Dossier")
     * Permet de gérer l'état du lock d'un dossier
     * @param Dossier $dossier
     * @param boolean $lock
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function manageLockDossier($lock, Dossier $dossier) {

        $lock = ($lock == 'true') ? true : false;
        $em = $this->getDoctrine()->getManager();
        $dossier->setLocked($lock);
        $em->persist($dossier);
        $em->flush();

        return $this->redirect($this->generateUrl('galerie_interne_global_managing'));
    }








    /**
     * Permet de modifier un dossier depuis la page global managing
     * @route("modify-dossier", name="interne_galerie_modify_dossier")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function modifyDossierAction(Request $request) {

        var_dump($request->request);

        $em      = $this->getDoctrine()->getManager();
        $dossier = $em->getRepository('InterneGalerieBundle:Dossier')->find($request->request->get('edit-dossier-id'));

        $emplacement = $request->request->get('dossier-edit-emplacement');
        $nom         = $request->request->get('dossier-edit-nom');
        $linked      = $request->request->get('dossier-edit-linked-groupe');

        if($emplacement != '') {

            if($emplacement == 'racine') $dossier->setParent(null);
            else {

                $parent = $em->getRepository('InterneGalerieBundle:Dossier')->find($emplacement);

                /*
                 * On vérifie que le nouvel emplacement ne soit pas dans la hierarchie actuelle du dit dossier
                 * pour éviter une boucle sans fin
                 */
                $hierarchie = $dossier->getEnfantsRecursive();
                $hierarchie[] = $dossier;

                if(!in_array($parent, $hierarchie)) $dossier->setParent($parent);
            }
        }

        if(str_replace(' ', '',$nom) != '')
            $dossier->setNom($nom);

        if($linked != '') {

            $groupe = $em->getRepository('AppBundle:Groupe')->find($linked);
            $dossier->setGroupe($groupe);
        }

        $em->persist($dossier);
        $em->flush();

        return $this->redirect($this->generateUrl('galerie_interne_global_managing'));
    }
}
