<?php

namespace Interne\ActiviteBundle\Controller;

use AppBundle\Utils\Entities\UtilGroupe;
use Interne\ActiviteBundle\Utils\UtilParticipants;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Interne\ActiviteBundle\Entity\Activite;
use Interne\ActiviteBundle\Entity\Depense;

use Interne\ActiviteBundle\Form\ActiviteType;
use Interne\ActiviteBundle\Form\DepenseType;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class ActiviteController extends Controller
{
    /**
     * Affiche la vue qui permet de créer une activité, et en valide une si cela est nécessaire (formulaire soumis)
     * @Route("nouvelle-activite", name="activite_creer_nouvelle")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function creerNouvelleActiviteAction(Request $request)
    {
        $activite       = new Activite();
        $type           = $this->createForm(new ActiviteType, $activite);
        $em             = $this->getDoctrine()->getManager();
        $groupes        = array($this->getConnectedMembre()->getActiveAttribution()->getGroupe());
        $groupes        = array_merge($groupes, $this->getConnectedMembre()->getActiveAttribution()->getGroupe()->getEnfantsRecursive());


        $type->handleRequest($request);

        /*
         * Test validation de formulaire
         */
        if($type->isValid()) { //Nouvelle Activité validable

            $organisateurs = $request->request->get('organisateurs_activite');
            $PGroupes      = $request->request->get('groupe_participant_activite');


            /*
             * On va ajouter chaque organisateur dans l'activité, en s'assurant qu'il n'y ait pas de doublon
             * En effet, vu qu'on ajoute aussi l'utilisateur courant, il se peut que ce blaireau se soit ajouté lui-même.
             */
            $activite->addOrganisateur($this->getConnectedMembre());

            if($organisateurs != null) {
                foreach ($organisateurs as $id) {

                    $organisateur = $em->getRepository('AppBundle:Membre')->find($id);
                    if (!$activite->getOrganisateurs()->contains($organisateur))
                        $activite->addOrganisateur($organisateur);
                }
            }

            shuffle($PGroupes);

            if($PGroupes != null) {


                /*
                 * On filtre les groupes pour éviter les doublons. Par exemple si le blaireau de chef d'activité à inclus
                 * Montfort et le Jean-Bart séparément, on va pas inclure le Jean-Bart dedans. Pour ce faire on sépare
                 * le groupe et ses enfants, puis on analyse séparément. (1) Si le groupe est déjà compris dans l'activité,
                 * on fait rien
                 * Ensuite on regarde si le groupe est parent d'un ou plusieurs groupes déjà inclus. Si c'est le cas on les
                 * enlève et on inclus le groupe.
                 */
                foreach($PGroupes as $id) {

                    $groupe             = $em->getRepository('AppBundle:Groupe')->find($id);
                    $enfants            = $groupe->getEnfantsRecursive();
                    $alreadyIncluded    = false;

                    // (1)
                    foreach($activite->getGroupes() as $includedGroup)
                        if($includedGroup == $groupe || in_array($groupe, $includedGroup->getEnfantsRecursive()))
                            $alreadyIncluded = true;


                    // (2)
                    $groupeIsParentOfIncluded = false;

                    $groupeEnfants = $groupe->getEnfantsRecursive();
                    foreach($activite->getGroupes() as $includedGroup) {

                        if (in_array($includedGroup, $groupeEnfants)) {

                            $groupeIsParentOfIncluded = true;
                            $activite->removeGroupe($includedGroup);
                        }
                    }

                    if(!$alreadyIncluded || $groupeIsParentOfIncluded)
                        $activite->addGroupe($groupe);


                }
            }

            $em->persist($activite);
            $em->flush();

            return $this->redirect($this->generateUrl('activite_dashboard', array('activite' => $activite->getId())));
        }


        /*
         * Aucun formulaire n'a été soumis ni validé, on génère donc la page
         */
        $chefs          = $this->getDoctrine()->getRepository('InterneSecurityBundle:User')->findChefs();

        return $this->render('InterneActiviteBundle:Activite:creer_nouvelle_activite.html.twig', array(

            'activiteForm' => $type->createView(),
            'chefs'        => $chefs,
            'groupes'      => $groupes
        ));
    }

    /**
     * @param Activite $activite
     * @return Response
     * @route("activite-dashboard/{activite}", name="activite_dashboard")
     * @ParamConverter("activite", class="InterneActiviteBundle:Activite")
     */
    public function activiteDashboardAction(Activite $activite) {

        $depense             = new Depense();
        $depenseCategorie    = $this->get('depensesCategories');
        $depenseForm         = $this->createForm(new DepenseType($depenseCategorie), $depense);
        $hierarchie          = array();
        $utilParticipants    = new UtilParticipants($activite);

        foreach($activite->getGroupes() as $groupe)
            $hierarchie = array_merge($hierarchie, $utilParticipants->findJSONChildren($groupe));


        return $this->render('InterneActiviteBundle:Activite:dashboard_activite.html.twig', array(

            'activite'              => $activite,
            'depenseForm'           => $depenseForm->createView(),
            'clientData'            => json_encode($hierarchie)
        ));
    }

    /**
     * Permet de retirer un organisateur de l'activité
     * @route("activite/organisateurs/remove/{activite}/{organisateur}", name="activite_remove_organisateur")
     * @ParamConverter("activite", class="InterneActiviteBundle:Activite")
     * @ParamConverter("organisateur", class="AppBundle:Membre")
     */
    public function removeOrganisateur(Activite $activite, \AppBundle\Entity\Membre $organisateur) {

        $em = $this->getDoctrine()->getManager();

        $activite->removeOrganisateur($organisateur);
        $em->persist($activite);
        $em->flush();

        return $this->redirect($this->generateUrl('activite_dashboard', array('activite' => $activite->getId())));
    }
}
