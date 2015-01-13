<?php

namespace AppBundle\Command;

use AppBundle\Entity\ObtentionDistinction;
use ClassesWithParents\F;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use AppBundle\Entity\Membre;
use AppBundle\Entity\Famille;
use AppBundle\Entity\Adresse;
use AppBundle\Entity\Attribution;
use AppBundle\Entity\Geniteur;


class PopulateCommand extends ContainerAwareCommand
{

    private $listeGroupes = null;
    private $listeFonctions = null;
    private $listeDistinctions = null;

    protected function configure()
    {
        $this
            ->setName('app:populate')
            ->setDescription('Remplir la base de donnée')
            ->addArgument('members', InputArgument::REQUIRED, 'Combien de membres souhaitez-vous génerer ?')            //nombre de membres souhaité
            ->addArgument('fonction', InputArgument::OPTIONAL, 'Abreviation de la fonction des attributions génerées')  //Abbreviation de la fonction des attributions souhaitées
            ->addArgument('type', InputArgument::OPTIONAL, 'ID du type des groupes des attributions génerées')          //ID du type de groupe souhaité
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        /** @var \Doctrine\ORM\EntityManager $em */
        $em                           = $this->getContainer()->get('doctrine.orm.entity_manager');
        $nombreDeMembresSouhaites     = intval($input->getArgument('members'));
        $baseFaker                    = \Faker\Factory::create();
        $frFaker                      = \Faker\Factory::create('fr_FR');
        $fonction                     = $input->getArgument('fonction');
        $type                         = $input->getArgument('type');


        /** @var \Symfony\Component\Console\Helper\ProgressHelper $progress */
        $progress    = $this->getHelperSet()->get('progress');

        $progress->start($output, $nombreDeMembresSouhaites);

        for($i = 0; $i < $nombreDeMembresSouhaites; $i++) {

            /*
             * En premier lieu, on crée une nouvelle famille. Dans cette famille, on va ajouter entre 1 et 4 gosse,
             * et entre 1 et 2 parents. Lorsqu'on a choisit le nombre de gosses à ajouter, on va incrémenter le nombre
             * de membres souhaité en tout pour que ce soit pris en compte
             */
            $famille = new Famille();
            $famille->setNom($baseFaker->lastName);
            $famille->setAdresse((mt_rand(1,11) > 4) ? $this->getRandomAdresse() : null);
            $famille->setValidity(mt_rand(0,2));
            $famille->setTelephone((mt_rand(0,10) > 4) ? $frFaker->phoneNumber : null);
            $famille->setEmail((mt_rand(1,10) > 7) ? $baseFaker->email : null);


            //Ajout des parents
            $needParent = 0;
            if(mt_rand(0,10) > 5)  //On lui file une mère
                $famille->setMere($this->getRandomGeniteur('f'));

            else $needParent += 10;

            if(mt_rand($needParent,10) > 5)  //On lui file un père
                $famille->setPere($this->getRandomGeniteur('m'));

            /*
             * Après avoir géré les parents, on va gérer les membres ainsi que leurs attributions respectives
             * afin qu'ils soient placés dans des groupes de manière efficace
             */
            $nbrDeGosses = 0;

            if(($nombreDeMembresSouhaites - $i) < 5)
                $nbrDeGosses = $nombreDeMembresSouhaites - $i;

            else
                $nbrDeGosses = mt_rand(1,5);

            $i += $nbrDeGosses;

            for($j = 0; $j < $nbrDeGosses; $j++) {

                $membre = $this->getRandomMember();
                $membre->addAttribution($this->getRandomAttribution($fonction, $type));

                for($k = 0; $k < mt_rand(0,3); $k++)
                    $membre->addDistinction($this->getRandomDistinction());

                $famille->addMembre($membre);
            }

            $em->persist($famille);

            $progress->advance();
        }

        $em->flush();

        $progress->finish();
    }

    /**
     * Génère une adresse bidon
     * @param boolean $facturable
     * @return Adresse
     */
    private function getRandomAdresse($facturable = null) {

        $faker   = \Faker\Factory::create('fr_FR');
        $adresse = new Adresse();

        $adresse->setLocalite($faker->city);
        $adresse->setNpa($faker->postcode);
        $adresse->setRue($faker->streetName . $faker->randomDigitNotNull);
        $adresse->setRemarques((mt_rand(1,100) > 80) ? $faker->text(120) : null);
        $adresse->setEmail((mt_rand(1,20) > 16) ? $faker->email : null);
        $adresse->setFacturable(  ($facturable == null) ? (  (mt_rand(1,10) > 7) ? true : false  )   : $facturable);

        return $adresse;
    }

    /**
     * Retourne un géniteur aléatoire
     * @param string $sexe le sexe du géniteur
     * @return Geniteur
     */
    private function getRandomGeniteur($sexe) {

        $geniteur = new Geniteur();
        $baseFaker= \Faker\Factory::create();

        $geniteur->setPrenom(($sexe == 'f') ? \Faker\Provider\fr_FR\Person::firstNameFemale() : \Faker\Provider\fr_FR\Person::firstNameMale());
        $geniteur->setEmail((mt_rand(1,10) > 6) ? $baseFaker->companyEmail : null);
        $geniteur->getProfession($this->getProfession());
        $geniteur->setAdresse((mt_rand(1,10) > 8) ? $this->getRandomAdresse() : null);
        $geniteur->setSexe($sexe);
        $geniteur->setTelephone((mt_rand(0,10) > 7) ? $baseFaker->phoneNumber : null);

        return $geniteur;
    }

    /**
     * Génère un membre aléatoire
     * @return Membre
     */
    private function getRandomMember() {

        $membre = new Membre();
        $sexe   = (mt_rand(1,10) > 5 ) ? 'f' : 'm';

        $frFaker   = \Faker\Factory::create('fr_FR');

        $membre->setSexe($sexe);
        $membre->setPrenom(($sexe == 'f') ? \Faker\Provider\fr_FR\Person::firstNameFemale() : \Faker\Provider\fr_FR\Person::firstNameMale());
        $membre->setEmail((mt_rand(1,10) > 6) ? $frFaker->email : null);
        $membre->setAdresse((mt_rand(1,100) > 95) ? $this->getRandomAdresse(true) : null);
        $membre->setNaissance($this->getRandomDateNaissance());
        $membre->setInscription($this->getRandomInscription());
        $membre->setValidity(mt_rand(0,2));
        $membre->setTelephone((mt_rand(0,10) > 3) ? $frFaker->phoneNumber : null);
        $membre->setNumeroAvs(mt_rand(111111111,999999999));
        $membre->setNumeroBs(mt_rand(0, 99999));
        $membre->setStatut((mt_rand(1,10) > 5) ? 'swaggé' : 'malheureusement pas swaggé');

        return $membre;
    }

    /**
     * Génère une attribution aléatoire
     * @param string $fonction l'abreviation de la fonction
     * @param id $type l'id du type de groupe souhaité
     * @return Attribution
     */
    private function getRandomAttribution($fonction = null, $type = null) {

        /** @var \Doctrine\ORM\EntityManager $em */
        $em          = $this->getContainer()->get('doctrine.orm.entity_manager');
        $attribution = new Attribution();

        if($this->listeFonctions == null) {

            if($fonction == null)
                $this->listeFonctions = $em->getRepository('AppBundle:Fonction')->findAll();

            else
                $this->listeFonctions = $em->getRepository('AppBundle:Fonction')->findByAbreviation($fonction);
        }

        if($this->listeGroupes == null) {

            if($type == null)
                $this->listeGroupes = $em->getRepository('AppBundle:Groupe')->findAll();

            else {
                $type = $em->getRepository('AppBundle:Type')->find($type);
                $this->listeGroupes = $em->getRepository('AppBundle:Groupe')->findByType($type);
            }
        }


        $attribution->setDateDebut($this->getRandomInscription());
        $attribution->setDateFin((mt_rand(1,10) > 7) ? new \Datetime( date('Y-m-d h:i:s', mt_rand(1428932408, 1513176008)) ) : null);
        $attribution->setFonction($this->listeFonctions[mt_rand(0, (count($this->listeFonctions)-1))]);
        $attribution->setGroupe($this->listeGroupes[mt_rand(0, (count($this->listeGroupes)-1))]);

        return $attribution;
    }

    /**
     * Retourne une distinction aléatoire
     * @return Distinction
     */
    private function getRandomDistinction() {

        $em          = $this->getContainer()->get('doctrine.orm.entity_manager');
        if($this->listeDistinctions == null)
            $this->listeDistinctions = $em->getRepository('AppBundle:Distinction')->findAll();

        $distinction = new ObtentionDistinction();
        $distinction->setDistinction($this->listeDistinctions[mt_rand(0, (count($this->listeDistinctions)-1))]);
        $distinction->setObtention($this->getRandomInscription());

        return $distinction;
    }

    /**
     * Retourne une date de naissance
     * @return \Datetime
     */
    private function getRandomDateNaissance() {

        return new \Datetime( date('Y-m-d h:i:s', mt_rand(537547208, 1229179208	)) );
    }

    /**
     * Retourne une date d'inscription
     * @return \Datetime
     */
    private function getRandomInscription() {

        return new \Datetime( date('Y-m-d h:i:s', mt_rand(1042468808, 1418481608 )) );
    }

    /**
     * Retourne une profession au hasard
     * @return string
     */
    private function getProfession() {

        $metiers = array(

            "Acheteur",
            "Acheteur d'espaces pub",
            "Acheteur Informatique Et Télécom",
            "Actuaire",
            "Adjoint administratif",
            "Adjoint administratif d'administration centrale",
            "Adjoint administratif territorial",
            "Adjoint technique de recherche et de formation",
            "Adjoint territorial d'animation",
            "Administrateur base de données",
            "Administrateur De Bases De Données",
            "Administrateur de biens",
            "Administrateur De Réseau",
            "Administrateur judiciaire",
            "Affréteur",
            "Agent administratif et agent des services techniques",
            "Agent d'entretien d'ascenseurs",
            "Agent d'exploitation des équipements audiovisuels",
            "Agent de maintenance en mécanique",
            "Agent de maîtrise",
            "Agent de police municipale",
            "Agent de réservation",
            "Agent des services techniques d'administration centrale",
            "Agent des services techniques de préfecture",
            "Agent des systèmes d'information et de communication",
            "Agent immobilier",
            "Agent spécialisé de police technique et scientifique",
            "Agent technique de recherche et de formation",
            "Aide comptable",
            "Aide de laboratoire",
            "Aide médico-psychologique",
            "Aide soignant",
            "Aide soignant",
            "Aide technique de laboratoire",
            "Ambulancier",
            "Analyste D'Exploitation",
            "Analyste financier",
            "Analyste programmeur",
            "Animateur",
            "Animateur",
            "Animateur de club de vacances",
            "Animateur de formation",
            "animateur environnement",
            "Animateur socioculturel",
            "Antiquaire",
            "Archéologue",
            "Architecte",
            "Architecte d'intérieur",
            "Architecte De Bases De Données",
            "Architecte De Réseau",
            "Architecte De Système D'Information",
            "Architecte Matériel",
            "Archiviste",
            "Artiste-peintre",
            "Assistant de conservation",
            "Assistant de justice",
            "Assistant de ressources humaines",
            "Assistant de service social",
            "Assistant de service social",
            "Assistant des bibliothèques",
            "Assistant ingénieur",
            "Assistant médico-technique",
            "Assistant socio-éducatif",
            "Assistant son",
            "Assistant vétérinaire",
            "Assistante de gestion PMI/PME",
            "Assistante maternelle",
            "Assistante sociale",
            "Astronome",
            "Attaché d?administration et d?intendance",
            "Attaché d?administration hospitalière",
            "Attaché d'administration centrale",
            "Attaché d'administration scolaire et universitaire",
            "Attaché de conservateur de patrimoine",
            "Attaché de police",
            "Attaché de préfecture",
            "Attaché de presse",
            "Auditeur Informatique",
            "Auteur-scénariste multimédia",
            "Auxiliaire de puériculture",
            "Auxiliaire de vie sociale",
            "Auxilliaire de vie",
            "Avocat",
            "Barman",
            "Bibliothécaire",
            "Bibliothécaire adjoint spécialisé",
            "Bijoutier joaillier",
            "Billettiste",
            "Bio-informaticien",
            "Biologiste, Vétérinaire, Pharmacien",
            "Bobinier de la construction électrique",
            "Boucher",
            "Boulanger",
            "Brasseur malteur",
            "Bronzier",
            "Bûcheron",
            "Cadreur",
            "Capitaine de Sapeur-Pompier",
            "Carreleur",
            "Carrossier réparateur",
            "Caviste",
            "Charcutier-traiteur",
            "Chargé de clientèle",
            "Chargé De Référencement",
            "Chargé de relations publiques",
            "Charpentier",
            "Chaudronnier",
            "Chef d'atelier des industries graphiques",
            "Chef de chantier",
            "Chef de comptoir",
            "Chef de fabrication",
            "Chef de produits voyages",
            "Chef De Projet - Project Manager",
            "Chef de projet informatique",
            "Chef de projet multimedia",
            "Chef de publicité",
            "Chef de rayon",
            "Chef de service de Police municipale",
            "Chef opérateur",
            "Chercheur",
            "Chercheur En Informatique",
            "Chirurgien-dentiste",
            "Chocolatier confiseur",
            "Clerc de notaire",
            "Coiffeur",
            "Comédien",
            "Commis de cuisine",
            "Commissaire de police",
            "Commissaire priseur",
            "comportementaliste",
            "Comptable",
            "Concepteur De Jeux Électroniques",
            "Concepteur rédacteur",
            "Concierge d'hôtel",
            "Conducteur",
            "Conducteur d'appareils",
            "Conducteur d'autobus",
            "Conducteur d'automobile",
            "Conducteur d'engins en BTP",
            "Conducteur de machine à imprimer d'exploitation complexe",
            "Conducteur de machine à imprimer simple",
            "Conducteur de machines",
            "Conducteur de machines agro",
            "Conducteur de station d'épuration",
            "Conducteur de taxi",
            "conducteur de train",
            "Conducteur de travaux",
            "Conducteur routier",
            "Conseil En Assistance À Maitrise D'Ouvrage",
            "Conseiller d'insertion et de probation",
            "Conseiller d'orientation",
            "Conseiller d'orientation-psychologue",
            "Conseiller en développement touristique",
            "Conseiller en économie sociale et familiale",
            "Conseiller socio-éducatif",
            "Conseiller territorial des activités physiques et sportives",
            "Conseillers principaux d'éducation",
            "Conservateur de bibliothèque",
            "Conservateur du patrimoine",
            "Consultant Communication & Réseaux",
            "Consultant E-Business",
            "Consultant En Conduite Du Changement",
            "Consultant En E-Learning",
            "Consultant En Gestion De La Relation Client",
            "Consultant En Organisation Des Systèmes D'Information",
            "Consultant En Technologies",
            "Consultant Erp",
            "Consultant Fonctionnel",
            "Consultant Informatique",
            "Contrôleur aérien",
            "Contrôleur de gestion",
            "Contrôleur de travaux",
            "Contrôleur des services techniques du matériel",
            "Contrôleur des systèmes d'information et de communication",
            "Contrôleur du travail",
            "Contrôleur en électricité et électronique",
            "Convoyeur de fonds",
            "Coordinatrice de crèches",
            "Correcteur",
            "Costumier-habilleur",
            "Courtier d'assurances",
            "Couvreur",
            "Créateur de parfum",
            "Cuisinier",
            "Cyberdocumentaliste",
            "Danseur",
            "Décorateur-scénographe",
            "Délégué médical",
            "Déménageur",
            "Démographe",
            "Dépanneur tv électroménager",
            "Designer automobile",
            "Dessinateur de presse",
            "Dessinateur industriel",
            "Détective privé",
            "Développeur",
            "Diététicien",
            "Directeur artistique",
            "Directeur Commercial",
            "Directeur d?établissement social et médico-social",
            "Directeur d?hôpital",
            "Directeur d'établissement d'enseignement artistique",
            "Directeur d'établissement sanitaire et social",
            "Directeur d'office de tourisme",
            "Directeur de collection",
            "Directeur de parc naturel",
            "Directeur De Projet",
            "Directeur de ressources humaines",
            "Directeur des soins",
            "Directeur Des Systèmes D'Information",
            "Directeur Technique",
            "Docker",
            "Documentaliste",
            "Douanier",
            "Ebéniste",
            "Eboueur",
            "Eco-conseiller",
            "Ecotoxicologue",
            "Educateur de jeunes enfants",
            "Educateur spécialisé",
            "Educateur sportif",
            "Educateur technique spécialisé",
            "Educateur territorial des activités physiques et sportives",
            "Electricien de maintenance",
            "Electricien du bâtiment",
            "Electricien électronicien auto",
            "Employé de groupage",
            "Employé de restauration rapide",
            "Employés du hall des hôtels",
            "Encadreur",
            "Enseignant Chercheur",
            "Entraîneur sportif",
            "Ergonome",
            "Ergonome",
            "Ergothérapeute",
            "Esthéticienne-cosméticienne",
            "Etalagiste décorateur",
            "Ethnologue",
            "Expert automobile",
            "Expert comptable",
            "Expert En Sécurité Informatique",
            "Facteur instrumental",
            "Femme de chambre ou valet de chambre",
            "Fleuriste",
            "Forfaitiste",
            "Formateur En Informatique",
            "Game designer",
            "Garçon de café",
            "Garde du corps",
            "Garde-champêtre",
            "Gardien d?immeuble",
            "Gardien d'immeuble",
            "Gardien de la paix",
            "Gendarme",
            "Géographe",
            "Géologue",
            "Géomètre topographe",
            "Gérant d'hôtel",
            "Gestionnaire De Parc Micro-Informatique",
            "Graphiste multimédia",
            "Greffier",
            "Guichetetier",
            "Guide accompagnateur",
            "Guide de haute montagne",
            "Guide Interprète",
            "Horiculteur",
            "Hot-Liner Technicien Help-Desk",
            "Hôtesse d'accueil",
            "Hôtesse de l'air",
            "Hotliner",
            "Huissier de justice",
            "Iconographe",
            "Infirmier",
            "Infirmier anesthésiste diplômé d?Etat",
            "Infirmier chef",
            "Infirmier de bloc opératoire diplômé d?Etat",
            "Infirmier diplômé d?Etat",
            "Infirmiere",
            "Ingénieur agroalimentaire",
            "Ingénieur Commercial",
            "Ingénieur d?études sanitaires",
            "Ingénieur d'étude et de développement",
            "Ingénieur d'études",
            "Ingénieur De Construction De Réseaux",
            "Ingénieur de laboratoire",
            "Ingénieur de production",
            "Ingénieur de recherche",
            "Ingénieur de recherche produit",
            "Ingénieur Déploiement De Réseau",
            "Ingénieur des services techniques du matériel",
            "Ingénieur des travaux",
            "Ingénieur Développement De Composants",
            "Ingénieur Développement Logiciels",
            "Ingénieur Développement Matériel Électronique",
            "ingénieur du génie rural des eaux et forêts",
            "Ingénieur du génie sanitaire",
            "Ingénieur du son",
            "Ingénieur en chef",
            "Ingénieur Intégration",
            "Ingénieur logistique",
            "Ingénieur Qualités Méthodes",
            "Ingénieur Sécurité",
            "Ingénieur Support Technique",
            "Ingénieur système-réseau",
            "Ingénieur Systèmes Et Réseaux",
            "Ingénieur Technico-Commercial",
            "Ingénieur Validation",
            "Inspecteur de l'action sanitaire et sociale",
            "Inspecteur des systèmes d'information et de communication",
            "Inspecteurs du travail",
            "Installateur en télécommunications",
            "Intégrateur Web",
            "Interprète",
            "Jeunes sapeurs-pompiers",
            "Journaliste",
            "Journaliste d'entreprise",
            "Journaliste radio",
            "Journaliste reporter",
            "Juge d'Instance",
            "Juge d'instruction",
            "Juge de Grande Instance",
            "Juge de l'application des peines",
            "Juge de l'exécution",
            "Juge des affaires familiales",
            "Juge des enfants",
            "Juriste Informatique",
            "Les adjoints administratifs de la police nationale",
            "Les adjoints de sécurité",
            "Les Directeurs de services pénitentiaires",
            "Les greffiers",
            "Les personnels de la protection judiciaire de la jeunesse",
            "Libraire",
            "Lieutenant de police",
            "Lieutenant de sapeurs-pompiers",
            "Livreur",
            "Maçon",
            "Magasinier en chef des bibliothèques",
            "Magistrat",
            "Maître chien",
            "Maître d'hôtel",
            "Maître de conférence",
            "Maître ouvrier des établissements d'enseignement",
            "Maître-nageur sauveteur",
            "Maîtres des établissements d'enseignement privés sous contrat",
            "Major de sapeur-pompier",
            "Manipulateur d?électroradiologie médicale",
            "Manutentionnaire cariste",
            "Maquettiste",
            "Masseur kinésithérapeute",
            "Mécanicien 2 roues",
            "Mécanicien auto",
            "Médecin",
            "Médecin de l'éducation nationale",
            "Médecin inspecteur de santé publique",
            "Médecin pharmacien de Sapeur-pompier",
            "Médecin Territorial",
            "Média-planner",
            "Médiateur social",
            "Menuisier",
            "Météorologue",
            "Métiers de la production",
            "Moniteur d'auto-école",
            "Moniteur de ski",
            "Moniteur éducateur",
            "Monteur",
            "Monteur électricien réseau edf",
            "Monteur en installations thermiques chauffagiste",
            "Musicien",
            "Netsurfer",
            "Notaire",
            "Océanographe",
            "Opérateur sur machine de production électrique",
            "Opérateur territorial des activités physiques et sportives",
            "Opératrice de saisie",
            "Opticien lunetier",
            "Orthophoniste",
            "Orthoptiste",
            "Ouvrier agricole",
            "Ouvrier d'entretien et d'accueil (OEA)",
            "Ouvrier d'État",
            "Ouvrier professionnel (OP)",
            "Ouvrier professionnel, maître-ouvrier",
            "Paramétreur De Progiciels",
            "Pâtissier",
            "Paysagiste",
            "Pédicure podologue",
            "Peintre en bâtiment",
            "Pépinieriste",
            "Personnel de surveillance",
            "Personnel pénitentaire d'insertion et de probation",
            "Personnel technique de l'administration pénitentiaire",
            "Pharmacien",
            "Pharmacien inspecteur de santé publique",
            "Photographe",
            "Pigiste",
            "Pilote d'avion",
            "Planneur stratégique",
            "Plombier",
            "Poissonnier",
            "Préparateur en pharmacie",
            "Préparateur en pharmacie avec Pub",
            "Professeur agrégé",
            "Professeur certifié",
            "Professeur d'arts plastiques",
            "Professeur d'éducation physique et sportive",
            "Professeur d'Université",
            "Professeur de lycée et collège",
            "Professeur de lycée professionnel",
            "Professeur de musique",
            "Professeur des écoles",
            "Professeur FLE",
            "Projectionniste",
            "Prothésiste",
            "Prothésiste dentaire",
            "Psychanalyste",
            "Psychologue",
            "Psychomotricien",
            "Puéricultrice",
            "Réalisateur",
            "Réceptionniste",
            "Rédacteur chef",
            "Rédacteur en assurances",
            "Rédacteur en chef",
            "Rédacteur Technique",
            "Rééducateur",
            "Régisseur",
            "Relieur-doreur",
            "Responsable d'agence bancaire",
            "Responsable D'Exploitation",
            "Responsable D'Un Système D'Information Métier",
            "Responsable de communication",
            "Responsable De Compte",
            "Responsable De Marketing Opérationnel",
            "Responsable De Service Informatique",
            "Responsable Des Études",
            "Responsable logistique",
            "Responsable marketing",
            "Responsable Sécurité Informatique",
            "Sage-Femme",
            "Sapeur pompier",
            "Sapeur-pompier",
            "Sapeur-Pompier Volontaire",
            "Scripte",
            "Secrétaire administratif",
            "Secrétaire administratif des affaires sanitaires et sociales",
            "Secrétaire assistante",
            "Secrétaire de Mairie",
            "Secrétaire de rédaction",
            "Secrétaire juridique",
            "Secrétaire médico-sociale",
            "Secrétariat administratif d'administration centrale",
            "Sécurité civile",
            "Serveur de restaurant",
            "Skippeur",
            "Sociologue",
            "Solier moquettiste",
            "Sommelier",
            "Soudeur",
            "Standardiste",
            "Stenotypiste",
            "Story-boarder",
            "Styliste",
            "Superviseur De Hot-Line",
            "Taxidermiste",
            "Technicien",
            "Technicien biologiste",
            "Technicien Chimiste",
            "Technicien de fabrication",
            "Technicien de l'éducation nationale",
            "Technicien de l'intervention sociale et familiale",
            "Technicien de labo photo",
            "Technicien de laboratoire",
            "Technicien en analyses biomédicales",
            "Technicien en mécanique",
            "Technicien forestier",
            "Technicien ligne haute tension",
            "Technicien maintenance auto",
            "Technicien Micro",
            "Technicien Réseau",
            "Technicien traitement déchets",
            "Techniciens de recherche et de formation",
            "Télévendeur",
            "Toiletteur",
            "Tôlier",
            "Tourneur-fraiseur",
            "Trader",
            "Traducteur",
            "Traffic Manager",
            "Urbaniste",
            "Vendeur en magasin",
            "Vendeur En Micro-Informatique",
            "Verrier",
            "Vétérinaire",
            "Viticulteur",
            "Webdesigner",
            "Webmarketeur",
            "Webmaster",
            "Webmestre",
            "Webplanner",
        );

        return $metiers[mt_rand(0, (count($metiers)-1))];
    }
}