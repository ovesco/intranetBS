<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


use AppBundle\Entity\Membre;
use AppBundle\Entity\Famille;

use AppBundle\Form\MembreType;
use AppBundle\Form\FamilleType;
use AppBundle\Form\MembreFamilleType;
use AppBundle\Form\MembreInfosScoutesType;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class MembreController extends Controller {

    /**
     * Affiche la page d'ajout de membre -> Membre/ajouter_membre.html.twig
     * et valide le formulaire si celui-ci est soumis
     * @Route("membre/ajouter", name="interne_ajouter_membre")
     */
    public function ajouterMembreAction(Request $request) {

        $membre             = new Membre();
        $famille            = new Famille();
        $membreForm         = $this->createForm(new MembreType, $membre);
        $membreFamilleForm  = $this->createForm(new MembreFamilleType(), $membre);
        $familleForm        = $this->createForm(new FamilleType, $famille);
        $em                 = $this->getDoctrine()->getManager();


        /*
         * La grosse galère pour valider de manière propre.
         * Fonctionnement :
         * - en premier lieu, la famille du membre est un formulaire différent (membreFamilleForm) qui contient
         *   seulement le champ famille
         * - Ensuite on traite d'abord le formulaire du membre n' sh*t
         * - Ensuite, si la valeur de membreFamille était NOEXIST (aucune famille), alors on valide le formulaire de famille
         * - Sinon, on récupère la bonne famille (id transmise par membreFamille)
         */
        $membreForm->handleRequest($request);

        if ($membreForm->isValid()) {

            $membre->setNaissance(new \Datetime($membre->getNaissance()));
            $membre->setValidity(0);

            if($membre->getAdresse()->getRue() == null) $membre->setAdresse(null);


            if($request->request->get('membre_famille')['famille'] == 'NOEXIST') {

                $familleForm->handleRequest($request);

                if ($familleForm->isValid()) {

                    $famille->setValidity(0);
                    $famille->getPere()->setSexe('m');
                    $famille->getMere()->setSexe('f');

                    /*
                     * On analyse les informations sur les geniteurs, pour savoir si on les set à null,
                     * ainsi que les informations sur les adresses
                     */
                    if($famille->getAdresse()->getRue() == null) $famille->setAdresse(null);
                    if($famille->getPere()->getPrenom() == null) $famille->setPere(null);
                    if($famille->getMere()->getPrenom() == null) $famille->setMere(null);
                    if($famille->getPere() != null && $famille->getPere()->getAdresse()->getRue() == null) $famille->getPere()->setAdresse(null);
                    if($famille->getMere() != null && $famille->getMere()->getAdresse()->getRue() == null) $famille->getMere()->setAdresse(null);

                    if($famille->getAdresse() == null && $famille->getMere()->getAdresse() == null && $famille->getPere()->getAdresse() == null)
                        throw new \Exception("Il ne peut pas y avoir AUCUNE adresse pour la famille");
                }
            }
            else
                $famille = $em->getRepository('AppBundle:Famille')->find($request->request->get('membre_famille')['famille']);

            $famille->addMembre($membre);
            $em->persist($famille);

            $em->flush();

            return $this->redirect($this->generateUrl('interne_voir_membre', array('membre' => $membre->getId())));
        }

        return $this->render('Membre/ajouter_membre.html.twig', array(

            'membreForm'        => $membreForm->createView(),
            'membreFamilleForm' => $membreFamilleForm->createView(),
            'familleForm'       => $familleForm->createView(),
        ));
    }




    /**
     * Cette fonction retourne une proprieté d'un membre donné par son id. la proprieté doit être du type param1__param2...
     * (getFamille()->getAdresse())
     * @param $membre Membre le membre
     * @param $property la proprieté à atteindre
     * @return mixed proprieté
     *
     * @route("ajax/membre/get-property/{membre}/{property}", name="interne_ajax_membre_get_property", options={"expose"=true})
     * @ParamConverter("membre", class="AppBundle:Membre")
     */
    public function getMembrePropertyAction(Membre $membre, $property) {

        $accessor = $this->get('accessor');
        $serializer = $this->get('jms_serializer');

        $data = $serializer->serialize($accessor->getProperty($membre, $property), 'json');
        return new JsonResponse($data);
    }



    /**
     * @route("/membre/voir/{membre}", name="interne_voir_membre", requirements={"membre" = "\d+"})
     * @ParamConverter("membre", class="AppBundle:Membre")
     */
    public function voirMembreAction($membre) {



        $membreForm         = $this->createForm(new MembreType, $membre);
        $infosScoutesForm   = $this->createForm(new MembreInfosScoutesType, $membre);

        return $this->render('Membre/voir_membre.html.twig', array(

                'membre'            => $membre,
                'listing'           => $this->get('listing'),
                'membreForm'        => $membreForm->createView(),
                'infosScoutesForm'  => $infosScoutesForm->createView()
            )
        );

        /*

        var_dump($this->get('app.twig.validation_extension')->validationFilter('"appbundle_membre_prenom"'));
        return new Response('');
        */

    }



    /**
     * @param $membre membre le membre
     * @param $type string 'attribution' ou 'distinction'
     * @param $obj int l'id de l'attribution ou distinction
     * @return jsonresponse
     * @route("membre/ajax/remove-attribution-or-distinction/{membre}/{type}/{obj}", name="interne_ajax_membre_remove_attr_dist", options={"expose"=true})
     * @ParamConverter("membre", class="AppBundle:Membre")
     */
    public function removeAttributionOrDistinctionAction(Membre $membre, $type, $obj) {

        $em   = $this->getDoctrine()->getManager();
        $enti = $em->getRepository('AppBundle:' . $type)->find($obj);

        $func = '';
        if($type == 'Attribution')
            $func = 'removeAttribution';
        else $func = 'removeDistinction';

        $membre->$func($enti);
        $em->persist($membre);
        $em->flush();

        return new JsonResponse(1);
    }


    /**
     * Vérifie si un numéro BS est déjà attribué ou pas
     * @param $numero le numéro BS
     * @return boolean
     * @route("membre/ajax/verify-numero-bs/{numero}", name="interne_membre_ajax_verify_numero_bs", options={"expose"=true}, requirements={"numero" = "\d+"})
     */
    public function isNumeroBsTakenAction($numero) {

        $num = $this->getDoctrine()->getRepository('AppBundle:Membre')->findByNumeroBs($numero);

        if($num == null) return new JsonResponse(false);
        else return new JsonResponse(true);
    }


    /**
     * Permet de modifier la famille d'un membre
     * @param $membre membre le membre
     * @param $famille famille la famille
     * @return jsonresponse
     * @ParamConverter("membre", class="AppBundle:Membre")
     * @ParamConverter("famille", class="AppBundle:Famille")
     * @route("membre/ajax/modify-famille/{membre}/{famille}", name="membre_modify_famille", options={"expose"=true})
     */
    public function modifyFamilleAction(Membre $membre, Famille $famille) {

        $em = $this->getDoctrine()->getManager();

        $old = $membre->getFamille();
        $old->removeMembre($membre);
        $famille->addMembre($membre);

        $em->persist($old);
        $em->persist($famille);
        $em->flush();

        return new JsonResponse('');
    }

    /**
     * Permet d'exporter la fiche en PDF
     * @param $membre le membre à exporter en PDF
     * @return Response le pdf
     * @ParamConverter("membre", class="AppBundle:Membre")
     * @route("membre/ajax/export/pdf/{membre}", name="membre_export_pdf", options={"expose"=true})
     */
    public function exportFicheToPdfAction(Membre $membre) {

        $pdf = $this->get('pdf');
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->defaultHeader();

        $pdf->ln(4); //Espace
        $pdf->SetFont('Arial','',20); //Famille du nom prénom
        $pdf->cell(0, 15, ucfirst($membre->getPrenom()) . ' ' . ucfirst($membre->getNom()), 'B'); //Nom Prénom
        $pdf->ln(20); //espace

        /*
         * On va créer un tableau contenant toutes les informations de base qu'on veut afficher, on pourra de cette
         * manière itérer dessus de manière simple, sans avoir à changer la police et tout à chaque fois
         */
        $infos = array(

            0 => array('titre' => 'Date de naissance', 'value' => $membre->getNaissance()->format('d.m.Y')),
            1 => array('titre' => 'Genre', 'value' => $membre->getSexe()),
            2 => array('titre' => 'Téléphone', 'value' => $membre->getTelephone()),
            3 => array('titre' => 'E-mail', 'value' => $membre->getEmail()),
            4 => array('titre' => 'Numéro AVS', 'value' => $membre->getNumeroAvs()),
            5 => array('titre' => 'Numéro BS', 'value' => $membre->getNumeroBs()),
            6 => array('titre' => 'Inscription', 'value' => $membre->getInscription()->format('d.m.Y')),
            7 => array('titre' => 'Statut', 'value' => $membre->getStatut()),
            8 => array('titre' => 'Fonction actuelle', 'value' => $membre->getActiveAttribution()->getFonction()->getNom()),
            9 => array('titre' => 'Unité actuelle', 'value' => $membre->getActiveAttribution()->getGroupe()->getNom())
        );

        $titleY = $pdf->getY();

        //Affichage des données du membre
        for($i = 0; $i < 5; $i++) {

            $pdf->SetFont('Arial','B',10);
            $pdf->cell(48,8,utf8_decode($infos[$i]['titre']));

            $pdf->setX(60);
            $pdf->SetFont('Arial','',10);
            $pdf->cell(50,8, $infos[$i]['value']);

            $pdf->ln(8);
        }

        $pdf->setY($titleY);
        for($i = 5; $i < count($infos); $i++) {

            $pdf->setX(120);
            $pdf->SetFont('Arial','B',10);
            $pdf->cell(50,8,utf8_decode($infos[$i]['titre']));

            $pdf->setX(160);
            $pdf->SetFont('Arial','',10);
            $pdf->cell(50,8, $infos[$i]['value']);

            $pdf->ln(8);
        }

        /*
         * après avoir géneré les informations de base, on génère l'adresse principale.
         */
        $pdf->ln(8);
        $pdf->SetFont('Arial','B',12);
        $pdf->cell(0,10,'Adresse principale');
        $pdf->ln(12);

        //Origines
        $pdf->SetFont('Arial','B',10);
        $pdf->cell(48,8,'Adresse de');
        $pdf->setX(60);
        $pdf->SetFont('Arial','',10);
        $pdf->cell(50,8, $membre->getAdressePrincipale()['origine']);
        $pdf->ln(8);

        //RUE
        $pdf->SetFont('Arial','B',10);
        $pdf->cell(48,8,'Rue');

        $pdf->setX(60);
        $pdf->SetFont('Arial','',10);
        $pdf->cell(50,8, $membre->getAdressePrincipale()['adresse']->getRue());
        $pdf->ln(8);

        //NPA
        $pdf->SetFont('Arial','B',10);
        $pdf->cell(48,8,'NPA');
        $pdf->setX(60);
        $pdf->SetFont('Arial','',10);
        $pdf->cell(50,8, $membre->getAdressePrincipale()['adresse']->getNpa());
        $pdf->ln(8);

        //Localité
        $pdf->SetFont('Arial','B',10);
        $pdf->cell(48,8,'Localité');
        $pdf->setX(60);
        $pdf->SetFont('Arial','',10);
        $pdf->cell(50,8, $membre->getAdressePrincipale()['adresse']->getLocalite());
        $pdf->ln(8);

        //Remarques
        $pdf->SetFont('Arial','B',10);
        $pdf->cell(48,8,'Remarques');
        $pdf->setX(60);
        $pdf->SetFont('Arial','',10);
        $pdf->cell(50,8, $membre->getAdressePrincipale()['adresse']->getRemarques());
        $pdf->ln(8);


        return new Response($pdf->output());

    }
}
