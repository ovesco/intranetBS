<?php

namespace Interne\FactureBundle\Controller;

use Interne\FactureBundle\Form\CreanceAddType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Interne\FactureBundle\Entity\Creance;
use Interne\FichierBundle\Entity\Membre;
use Symfony\Component\Validator\Constraints\DateTime;
use Interne\FactureBundle\Entity\Facture;




class CreanceController extends Controller
{
    /*
     * Supprime une cérance en ajax.
     * Ne supprime que les cérances qui sont pas encore
     * liée a une facture.
     */
    /**
     * @return Response
     */
    public function deleteAjaxAction()
    {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {

            $id = $request->request->get('idCreance');
            $em = $this->getDoctrine()->getManager();
            $creance = $em->getRepository('InterneFactureBundle:Creance')->find($id);

            /*
             * On vérifie que la cérance n'est pas liée à une facture avant de la supprimer
             */
            if(!$creance->isFactured())
            {
                $em->remove($creance);
                $em->flush();
            }

            return $this->render('InterneFactureBundle:viewForFichierBundle:interfaceForFamilleOrMembre.html.twig',
                array('ownerEntity' => $creance->getOwner()));
        }
    }


    /*
     * Ajoute des cérances en masse à la liste de membre (listing)
     *
     */
    /**
     * @return Response
     */
    public function addCreanceToListingAjaxAction()
    {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {

            $creance = new Creance();
            $creanceAddForm  = $this->createForm(new CreanceAddType,$creance);
            $creanceAddForm->submit($request);
            $creance = $creanceAddForm->getData();

            /*
             * On récupère les proporiété de la créance du formulaire
             */
            $titre = $creance->getTitre();
            $remarque = $creance->getRemarque();
            $montant = $creance->getMontantEmis();

            /*
             * On récupère la liste des ids de membre à qui ajouter une créance
             */
            $listeIds = $creanceAddForm->get('idsMembre');


            /*
             * On ajoute une cérance à chaque membre de la liste
             */
            foreach ($listeIds as $idMembre) {
                $creance = new Creance();
                $creance->setDateCreation(new \DateTime());
                $creance->setMontantEmis($montant);
                $creance->setRemarque($remarque);
                $creance->setTitre($titre);

                $em = $this->getDoctrine()->getManager();
                $membre = $em->getRepository('InterneFichierBundle:Membre')->find($idMembre);

                $membre->addCreance($creance);

                $em->persist($creance);
                $em->flush();
            }

            return new Response();

        }
        return new Response();
    }

    /*
     * Ajoute une cérance à un membre ou une famille
     */
    /**
     * @return Response
     */
    public function addAjaxAction()
    {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {

            $creance = new Creance();
            $creance->setDateCreation(new \DateTime());

            $creanceAddForm  = $this->createForm(new CreanceAddType,$creance);

            $creanceAddForm->submit($request);

            $creance = $creanceAddForm->getData();

            $classOwner = $creanceAddForm->get('classOwner')->getData();
            $idOwner = $creanceAddForm->get('idOwner')->getData();


            $em = $this->getDoctrine()->getManager();
            if ($classOwner == 'Membre') {
                $membre = $em->getRepository('InterneFichierBundle:Membre')->find($idOwner);

                $membre->addCreance($creance);

            }
            elseif ($classOwner == 'Famille') {
                $famille = $em->getRepository('InterneFichierBundle:Famille')->find($idOwner);

                $famille->addCreance($creance);
            }

            $em->persist($creance);
            $em->flush();

            return $this->render('InterneFactureBundle:viewForFichierBundle:interfaceForFamilleOrMembre.html.twig',
                array('ownerEntity' => $creance->getOwner()));

        }
        return new Response();
    }

    /*
     * Creation de factures avec une liste de créances (Id).
     *
     * Remarque: cette fonction va grouper les factures par unité de
     * facturation. Cela marche uniquement pour les factures
     * présente dans la liste d'IDs
     */
    /**
     * @param Array $listeIdCreance
     */
    private function createFacture($listeIdCreance)
    {


        /*
         * On load la base de donnée
         */
        $em = $this->getDoctrine()->getManager();
        $creanceRepo = $em->getRepository('InterneFactureBundle:Creance');

        /*
         * On va mettre les créance de la liste dans des facture
         */

        foreach ($listeIdCreance as $creanceId) {
            $creance = $creanceRepo->find($creanceId);
            /*
             * La fonction va parcourire la liste des creances mais il se peut que
             * la facturation aie été déjà faite dans une itération précédente.
             * On va donc s'assurer que la créance n'est pas encore liée à une
             * facture.
             */
            if ($creance->getFacture() == null) {
                /*
                 * On commence par regarder si la créance
                 * appartien à un membre ou une famille.
                 * Ainsi que déterminer la cible de facturation
                 */
                $famille = $creance->getFamille();
                $membre = $creance->getMembre();

                $cibleFacturation = '';

                if ($famille != null) {
                    /*
                     * la créance appartien à une famille
                     */
                    $cibleFacturation = 'Famille';
                } elseif ($membre != null) {
                    /*
                     * la cérance appartient à un membre
                     */
                    $cibleFacturation = $membre->getEnvoiFacture(); //retourne soit 'Famille' soit 'Membre'
                    if ($cibleFacturation == 'Famille') {
                        //on récupère la famille du membre
                        $famille = $membre->getFamille();
                    }
                }

                /*
                 * Creation de la nouvelle facture
                 */
                $facture = new Facture();
                $facture->setMontantRecu(0);
                $facture->setDateCreation(new \DateTime());
                $facture->setStatut('ouverte');


                /*
                 * On procède de manière différente selon
                 *  la cible de facturation.
                 */

                switch ($cibleFacturation) {

                    case 'Membre':

                        foreach ($membre->getCreances() as $linkedCreance) {
                            /*
                             * On récupère toute les créances du membre
                             * qui ne sont pas encore facturée
                             * !!! Et qui apparitennent à la liste !!!
                             */
                            if ((!$linkedCreance->isFactured()) && in_array($linkedCreance->getId(), $listeIdCreance)) {
                                $facture->addCreance($linkedCreance);
                            }
                        }
                        $membre->addFacture($facture);

                        break;

                    case 'Famille':

                        foreach ($famille->getCreances() as $linkedCreance) {
                            /*
                             * On récupère toute les créances de la famille
                             * qui ne sont pas encore facturée
                             * !!! Et qui apparitennent à la liste !!!
                             */
                            if ((!$linkedCreance->isFactured()) && in_array($linkedCreance->getId(), $listeIdCreance)) {
                                $facture->addCreance($linkedCreance);
                            }
                        }

                        foreach ($famille->getMembres() as $membreOfFamille) {
                            /*
                             * On recherche des créances chez les
                             * membre de la famille qui envoie
                             * leurs facture à la famille
                             */
                            if ($membreOfFamille->getEnvoiFacture() == 'Famille') {
                                foreach ($membreOfFamille->getCreances() as $linkedCreance) {
                                    /*
                                     * On récupère toute les créances du membre
                                     * qui ne sont pas encore facturée
                                     * !!! Et qui apparitennent à la liste !!!
                                     */
                                    if ((!$linkedCreance->isFactured()) && in_array($linkedCreance->getId(), $listeIdCreance)) {
                                        $facture->addCreance($linkedCreance);
                                    }
                                }
                            }
                        }

                        $famille->addFacture($facture);
                        break;

                }

                $em->persist($facture);
                $em->flush();
            }
        }
    }

    /*
     * Cette methode permet de facturer une liste de cérance
     * depuis plusieur page différente.
     */
    /**
     * @return Response
     */
    public function facturationAjaxAction()
    {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {

            /*
             * On récupère les données
             */
            $fromPage = $request->request->get('fromPage');
            $listeIdCreance = $request->request->get('listeCreance');

            //cération des nouvelles factures
            $this->createFacture($listeIdCreance);


            /*
             * On load la base de donnée
             */
            $em = $this->getDoctrine()->getManager();
            $creanceRepo = $em->getRepository('InterneFactureBundle:Creance');

            /*
             * On charge juste la premier créance
             * c'est suffisant pour retrouver la
             * famille ou le membre.
             */
            $creance = $creanceRepo->find($listeIdCreance[0]);


            //adaptation du rendu selon la provenance
            if ($fromPage == 'Search') {
                return new Response();
            } elseif ($fromPage == 'Membre') {

                return $this->render('InterneFactureBundle:viewForFichierBundle:interfaceForFamilleOrMembre.html.twig',
                    array('ownerEntity' => $creance->getOwner()));


            } elseif ($fromPage == 'Famille') {

                return $this->render('InterneFactureBundle:viewForFichierBundle:interfaceForFamilleOrMembre.html.twig',
                    array('ownerEntity' => $creance->getOwner()));

            }

        }
        return new Response();
    }


    /*
     * Crée un rendu twig custum en fonction de la page qui
     * demande de formulaire (modal) pour l'ajout de créance.
     *
     */
    /**
     * @param $ownerEntity
     * @return Response
     */
    public function creanceModalFormAction($ownerEntity)
    {
        $creance = new Creance();

        $creanceAddForm  = $this->createForm(new CreanceAddType,$creance);

        /*
         * On récupère les infos du membre ou famille pour construire
         * le formulaire custum de la page qui le demande..
         */
        if($ownerEntity == null)
        {
            //si l'entité est null c'est que c'est pour le formulaire de listing
        }
        else if($ownerEntity->isClass('Membre'))
        {
            $creanceAddForm->get('idOwner')->setData($ownerEntity->getId());
            $creanceAddForm->get('classOwner')->setData('Membre');
        }
        else if($ownerEntity->isClass('Famille'))
        {
            $creanceAddForm->get('idOwner')->setData($ownerEntity->getId());
            $creanceAddForm->get('classOwner')->setData('Famille');
        }

        return $this->render('InterneFactureBundle:viewForFichierBundle:modalForm.html.twig',
            array('ownerEntity' => $ownerEntity, 'creanceForm' => $creanceAddForm->createView() ));

    }
}