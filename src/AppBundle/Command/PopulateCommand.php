<?php

namespace AppBundle\Command;

use AppBundle\Entity\Categorie;
use AppBundle\Entity\Contact;
use AppBundle\Entity\ObtentionDistinction;
use AppBundle\Entity\Personne;
use AppBundle\Entity\Telephone;

use AppBundle\Entity\Mail;
use AppBundle\Entity\ReceiverFamille;
use AppBundle\Entity\ReceiverMembre;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use AppBundle\Command\ConsoleOutput as CustomOutput;

use AppBundle\Entity\Membre;
use AppBundle\Entity\Famille;
use AppBundle\Entity\Adresse;
use AppBundle\Entity\Attribution;

use AppBundle\Entity\Fonction;
use AppBundle\Entity\Distinction;
use AppBundle\Entity\Model;
use AppBundle\Entity\Groupe;
use AppBundle\Entity\Pere;
use AppBundle\Entity\Mere;


/* FinanceBundle */
use AppBundle\Entity\Rappel;
use AppBundle\Entity\Facture;
use AppBundle\Entity\Creance;
use AppBundle\Entity\DebiteurFamille;
use AppBundle\Entity\DebiteurMembre;


class PopulateCommand extends ContainerAwareCommand
{
    /** @var \Doctrine\ORM\EntityManager $em */
    protected $em;

    /** @var integer */
    protected $numberOfMember;

    /** @var  CustomOutput */
    protected $output;

    protected function configure()
    {
        $this
            ->setName('app:populate')
            ->setDescription('Remplir la base de donnée avec des données aléatoire')
            ->addArgument('members', InputArgument::OPTIONAL, 'Combien de membres souhaitez-vous génerer (default 200) ?',150)
        ;
    }

    /**
     * this method is executed before execute() and
     * used to setup variables used in the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->output = new CustomOutput($output);
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->numberOfMember = intval($input->getArgument('members'));

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->createDistinctions();
        $this->output->info('Created: Distinctions')->writeln();
        $this->createFonctions();
        $this->output->info('Created: Fonctions')->writeln();
        $this->createCategories();
        $this->output->info('Created: Categories')->writeln();
        $this->createModels();
        $this->output->info('Created: Models')->writeln();
        $this->createHierarchie(null,$this->getGroupes());
        $this->output->info('Created: Groupes')->writeln();
        $this->output->info('Start adding Members')->writeln();
        $this->createMembers();
        $this->output->info('Finish adding Members')->writeln();

    }

    /**
     * Cette fonction crée la hierarchie des groupes définit dans la fonction getGroupes.
     * Elle doit être appelée après la création des fonctions et des disintinctions.
     *
     * @param $parent
     * @param $childsGroupes
     */
    private function createHierarchie($parent,$childsGroupes){

        foreach($childsGroupes as $name => $groupeData)
        {

            $groupe = $this->em->getRepository('AppBundle:Groupe')->findOneByNom($name);

            if($groupe == null)
            {
                //création si inexistant
                $groupe = new Groupe();
                $groupe->setNom($name);
            }

            $model = $this->em->getRepository('AppBundle:Model')->findOneBy(array('nom'=>$groupeData[0]));

            $groupe->setModel($model);
            $groupe->setParent($parent);
            $groupe->setActive(true);

            //next sub level
            $childs = $groupeData[1];
            $this->createHierarchie($groupe,$childs);


            $this->em->persist($model);
            $this->em->persist($groupe);
            $this->em->flush();

        }

    }

    private function getGroupes(){

        $groupes = array(
            'Brigade de Sauvabelin' => array(
                'Brigade', array(
                    'Eclaireurs' => array(
                        'Branche', array(
                            'Berisal'=> array(
                                'Troupe', array(
                                    'Faucons'=> array(
                                        'Patrouille', array()
                                    ),
                                    'Cerfs'=> array(
                                        'Patrouille', array(),
                                    ),
                                    'Panthère'=> array(
                                        'Patrouille', array(),
                                    ),
                                ),
                            ),
                            'Montfort'=> array(
                                'Troupe', array(
                                    'Fregate'=> array(
                                        'Patrouille', array()
                                    ),
                                    'Optimiste'=> array(
                                        'Patrouille', array(),
                                    ),
                                    'Galion'=> array(
                                        'Patrouille', array(),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'Eclaireuses' => array(
                        'Branche', array(
                            'Solalex'=> array(
                                'Troupe', array(
                                    'Hirondelles'=> array(
                                        'Patrouille', array()
                                    ),
                                    'Daufins'=> array(
                                        'Patrouille', array(),
                                    ),
                                    'Tigresses'=> array(
                                        'Patrouille', array(),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'Louveteaux' => array(
                        'Branche', array(
                            'Mont-d\'or'=> array(
                                'Meute', array(
                                    'Blattes'=> array(
                                        'Sixaine', array()
                                    ),
                                    'Ours'=> array(
                                        'Sixaine', array(),
                                    ),
                                    'Loup'=> array(
                                        'Sixaine', array(),
                                    ),
                                ),
                            ),
                            'Clairière'=> array(
                                'Meute', array(
                                    'Chèvres'=> array(
                                        'Sixaine',array()
                                    ),
                                    'Chats'=> array(
                                        'Sixaine', array(),
                                    ),
                                    'Lapins'=> array(
                                        'Sixaine', array(),
                                    ),
                                ),
                            ),
                        ),
                    ),
                )
            ),
        );

        return $groupes;

    }

    private function createDistinctions(){
        $distinctions = array(
            'Cravate Bleu'=>'La cravate bleu est une distinctions que tout les chefs d\'unité recoivent',
            '1er classe'=>'Distinction obtenue suite à la réalisaiton d\'une épreuve technique',
            'Badge feu'=>'',
            'Badge topographie'=>'',
            'Badge cuisine'=>'',
            'Aspirant'=>'',
        );
        foreach($distinctions as $distinction => $remarque)
        {
            $distinctionInBDD = $this->em->getRepository('AppBundle:Distinction')->findOneByNom($distinction);
            if($distinctionInBDD == null)
            {
                $new = new Distinction();
                $new->setNom($distinction);
                $new->setRemarques($remarque);
                $this->em->persist($new);
            }
        }
        $this->em->flush();
    }

    private function createFonctions(){
        $fonctions = array(
            'Commandant'=>'CDT',
            'Quartier-maitre'=>'QM',
            'Chef matériel'=>'Mat',
            'Chef de Branche'=>'CB',
            'Chef de Branche adjoint'=>'CBA',
            'Chef de Troupe'=>'CT',
            'Chef de Troupe adjoint'=>'CTA',
            'Chef de Patrouille'=>'CP',
            'Chef de Meute'=>'CM',
            'Chef Louvetaux'=>'CL',
            'Adjoint'=>'Adj',
            'Eclaireur'=>'Ecl',
            'Eclaireuse'=>'Eclse',
            'Louveteaux'=>'Lvtx',
            'Louvettes'=>'Lvttes',
        );
        foreach($fonctions as $fonction => $abrev)
        {
            $fonctionInBDD = $this->em->getRepository('AppBundle:Fonction')->findOneByNom($fonction);
            if($fonctionInBDD == null)
            {
                //création si inexistant
                $new = new Fonction();
                $new->setNom($fonction);
                $new->setAbreviation($abrev);
                $this->em->persist($new);
            }
        }
        $this->em->flush();
    }

    private function createCategories(){
        $categories = array(
            'Brigade'=>'description',
            'Branche'=>'description',
            'Unité'=>'description',
            'Sous-unité'=>'description',
        );
        foreach($categories as $categorie => $descr)
        {
            $categorieInBDD = $this->em->getRepository('AppBundle:Categorie')->findOneByNom($categorie);
            if($categorieInBDD == null)
            {
                //création si inexistant
                $new = new Categorie();
                $new->setNom($categorie);
                $new->setDescription($descr);
                $this->em->persist($new);
            }
        }
        $this->em->flush();
    }

    /**
     * cette fonction doit etre executée après la création
     * des fonctions et des catégories.
     */
    private function createModels(){
        $models = array(
            'Brigade'=>array(
                'Categorie'=>'Brigade',
                'FonctionChef'=>'CDT',
                'Fonctions'=>array('QM','Mat')
            ),
            'Branche'=> array(
                'Categorie'=>'Branche',
                'FonctionChef'=>'CB',
                'Fonctions'=>array('CBA')
            ),
            'Troupe'=> array(
                'Categorie'=>'Unité',
                'FonctionChef'=>'CT',
                'Fonctions'=>array('Adj','CTA')
            ),
            'Meute'=> array(
                'Categorie'=>'Unité',
                'FonctionChef'=>'CM',
                'Fonctions'=>array('Adj')
            ),
            'Patrouille'=> array(
                'Categorie'=>'Sous-unité',
                'FonctionChef'=>'CP',
                'Fonctions'=>array('Ecl')
            ),
            'Sixaine'=> array(
                'Categorie'=>'Sous-unité',
                'FonctionChef'=>'CL',
                'Fonctions'=>array('Lvtx')
            ),
        );
        foreach($models as $modelName => $infos)
        {
            $modelInBDD = $this->em->getRepository('AppBundle:Model')->findOneByNom($modelName);
            if($modelInBDD == null)
            {
                //création si inexistant
                $new = new Model();
                $new->setNom($modelName);
                $categorie = $this->em->getRepository('AppBundle:Categorie')->findOneByNom($infos['Categorie']);
                $new->addCategorie($categorie);
                $fonctionChef = $this->em->getRepository('AppBundle:Fonction')->findOneByAbreviation($infos['FonctionChef']);
                $new->setFonctionChef($fonctionChef);

                $fonctions = $this->em->getRepository('AppBundle:Fonction')->findByAbreviation($infos['Fonctions']);
                foreach($fonctions as $fonction)
                {
                    $new->addFonction($fonction);
                    $this->em->persist($fonction);
                }

                $new->setAffichageEffectifs(true);

                $this->em->persist($fonctionChef);
                $this->em->persist($categorie);
                $this->em->persist($new);
            }
        }
        $this->em->flush();
    }

    /**
     * Génère une adresse aléatoire
     * @param $canBeNull
     * @return Adresse
     */
    private function getRandomAdresse($canBeNull = false) {

        $faker   = \Faker\Factory::create('fr_CH');
        $adresse = new Adresse();

        $adresse->setExpediable( (mt_rand(0,1) == 0) ? true : false );
        $adresse->setLocalite($faker->city);
        $adresse->setNpa(substr($faker->postcode, 0, 4));
        $adresse->setRue($faker->streetName . ' ' . $faker->randomDigitNotNull);
        $adresse->setRemarques($this->getText(100,true));


        if($canBeNull)
            return (rand(0,1) == 1) ? $adresse : null;
        else
            return $adresse;
    }


    /**
     * @return Pere
     */
    private function getRandomPere() {

        $geniteur = new Pere();

        $geniteur->setPrenom($this->getPrenom('m'));
        $geniteur->setProfession($this->getProfession(true));
        $geniteur->setContact($this->getRandomContact());
        $geniteur->setSexe(Personne::HOMME);
        $geniteur->setIban($this->getIban(true));

        return $geniteur;
    }

    /**
     * @return Mere
     */
    private function getRandomMere() {

        $geniteur = new Mere();

        $geniteur->setPrenom($this->getPrenom(Personne::FEMME));
        $geniteur->setProfession($this->getProfession(true));
        $geniteur->setContact($this->getRandomContact());
        $geniteur->setSexe(Personne::FEMME);
        $geniteur->setIban($this->getIban(true));

        return $geniteur;
    }

    /**
     * Génère un membre aléatoire
     * @return Membre
     */
    private function getRandomMember() {

        $membre = new Membre();
        $sexe   = (mt_rand(1,10) > 5 ) ? Personne::FEMME : Personne::HOMME;

        $frFaker   = \Faker\Factory::create('fr_CH');

        $membre->setSexe($sexe);
        $membre->setPrenom($this->getPrenom($sexe));
        $membre->setContact($this->getRandomContact());
        $membre->setNaissance($this->getRandomDateNaissance());
        $membre->setInscription($this->getRandomInscription());
        $membre->setValidity(mt_rand(0,2));
        $membre->setNumeroAvs(mt_rand(111111111,999999999));
        $membre->setNumeroBs(mt_rand(0, 99999));
        $membre->setStatut($this->getStatut());
        $membre->setIban($this->getIban(true));

        return $membre;
    }

    /**
     * Génère une attribution aléatoire
     * @return Attribution
     */
    private function addRandomAttribution(Membre $membre) {

        $attribution = new Attribution();

        $listeGroupes = $this->em->getRepository('AppBundle:Groupe')->findAll();

        $attribution->setDateDebut($this->getRandomInscription());
        $attribution->setDateFin((mt_rand(1,10) > 7) ? new \Datetime( date('Y-m-d h:i:s', mt_rand(1428932408, 1513176008)) ) : null);

        /** @var Groupe $groupe */
        $groupe = $listeGroupes[mt_rand(0, (count($listeGroupes)-1))];//on prend un groupe aleatoire
        $attribution->setGroupe($groupe);
        $attribution->setMembre($membre);

        /*
         * On regarde si y a un chefs dans le groupe. Si pas encore, alors on met le membre
         * comme chefs.
         * Si deja un chef, alors on met le membre dans une des attributions possible du groupe
         */
        if($groupe->getChef() == null)
        {
            $attribution->setFonction($groupe->getModel()->getFonctionChef());
        }
        else
        {
            $fonctions = $groupe->getModel()->getFonctions();
            $attribution->setFonction($fonctions[mt_rand(0, (count($fonctions)-1))]);
        }
        $this->em->persist($membre);
        $this->em->persist($groupe);
        $this->em->persist($attribution);
        return $attribution;
    }

    /**
     * Retourne une distinction aléatoire
     * @return Distinction
     */
    private function getRandomDistinction() {

        $listeDistinctions = $this->em->getRepository('AppBundle:Distinction')->findAll();

        $distinction = new ObtentionDistinction();
        $distinction->setDistinction($listeDistinctions[mt_rand(0, (count($listeDistinctions)-1))]);
        $distinction->setDate($this->getRandomInscription());

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
     * @param boolean $canBeNull
     */
    private function getProfession($canBeNull = false) {

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

        $randIndex = mt_rand(0, (count($metiers)-1));

        if($canBeNull)
            return (rand(0,1) == 1) ? $metiers[$randIndex] : null;
        else
            return $metiers[$randIndex];
    }

    /**
     * @param bool $canBeNull
     * @return null|string
     */
    private function getNom($canBeNull = false) {

        $baseFaker= \Faker\Factory::create();

        if($canBeNull)
            return (rand(0,1) == 1) ? $baseFaker->lastName : null;
        else
            return $baseFaker->lastName;

    }

    /**
     * @param $sexe
     * @param bool $canBeNull
     * @return mixed|null
     */
    private function getPrenom($sexe, $canBeNull = false) {

        if($sexe == Personne::FEMME)
            $prenom = \Faker\Provider\fr_FR\Person::firstNameFemale();
        else
            $prenom = \Faker\Provider\fr_FR\Person::firstNameMale();

        if($canBeNull)
            return (rand(0,1) == 1) ? $prenom : null;
        else
            return $prenom;

    }

    /**
     * @param bool $canBeNull
     * @return null|string
     */
    private function getPhone($canBeNull = false){

        $frFaker = \Faker\Factory::create('fr_CH');

        if($canBeNull)
            return (rand(0,1) == 1) ? $frFaker->phoneNumber : null;
        else
            return $frFaker->phoneNumber;

    }

    /**
     * @param bool $canBeNull
     * @return null|string
     */
    private function getAdresseEmail($canBeNull = false){

        $frFaker = \Faker\Factory::create('fr_CH');

        if($canBeNull)
            return (rand(0,1) == 1) ? $frFaker->email : null;
        else
            return $frFaker->email;

    }

    /**
     * @param bool $canBeNull
     * @return null|string
     */
    private function getIban($canBeNull = false){

        $iban = 'CH'.mt_rand(111111111,999999999);

        if($canBeNull)
            return (rand(0,1) == 1) ? $iban : null;
        else
            return $iban;
    }

    /**
     * @param $lenght
     * @param bool $canBeNull
     * @return null|string
     */
    private function getText($lenght, $canBeNull = false)
    {
        $faker   = \Faker\Factory::create('fr_CH');
        $text = $faker->text($lenght);
        if($canBeNull)
            return (rand(0,1) == 1) ? $text : null;
        else
            return $text;
    }

    /**
     * @return mixed
     */
    private function getStatut()
    {
        $statut = array('Inscrit','Préinscrit','Désincrit');
        $randIndex = mt_rand(0, (count($statut)-1));
        return $statut[$randIndex];
    }


    /**
     * Retourne une date
     * @return \Datetime
     */
    private function getRandomDate() {

        return new \Datetime( date('Y-m-d h:i:s', mt_rand(1042468808, 1418481608 )) );
    }

    /**
     * @param $debiteur
     * @param bool $payee
     * @return Creance
     */
    private function getCreance($debiteur, $payee = false){

        $creance = new Creance();
        $creance->setDebiteur($debiteur);


        $annee = mt_rand(2000,2015);
        $periode = array('hiver','printemps','été','automne');

        $creance->setTitre((mt_rand(0,1) == 0) ? 'Cotisation '.$annee : 'Camp '.$periode[mt_rand(0,3)].' '.$annee);
        $creance->setRemarques($this->getText(120,true));
        $creance->setMontantEmis(mt_rand(1,300));
        $creance->setDateCreation($this->getRandomDate());
        if($payee)
        {
            $montant = mt_rand(1000,3000)/10;

            $creance->setMontantRecu($montant);
        }
        return $creance;

    }

    /**
     * @param $debiteur
     * @return Facture
     */
    private function getFacture($debiteur){


        $facture = new Facture();
        $facture->setDebiteur($debiteur);

        $dateCreation = $this->getRandomDate();
        $facture->setDateCreation($dateCreation);

        $mois = mt_rand(1,24);
        $jours = mt_rand(1,30);
        $interval = new \DateInterval('P'.$mois.'M'.$jours.'D');

        $datePayement = clone $dateCreation->add($interval);

        $nbCreance = mt_rand(1,3);
        if(mt_rand(0,1) == 1)
        {
            //payee
            for($n = 0; $n < $nbCreance; $n++) {
                $facture->addCreance($this->getCreance($debiteur,true));
            }
            $facture->setStatut(Facture::PAYED);
            $facture->setDatePayement($datePayement);
            for($n = 0; $n < mt_rand(0,6); $n++) {
                $rappel = new Rappel();
                $rappel->setMontantEmis(mt_rand(0,5));
                $rappel->setMontantRecu(mt_rand(0,5));
                $rappel->setDateCreation($dateCreation);
                $facture->addRappel($rappel);
            }
        }
        else
        {
            //ouverte
            for($n = 0; $n < $nbCreance; $n++) {
                $facture->addCreance($this->getCreance($debiteur));
            }
            for($n = 0; $n < mt_rand(0,6); $n++) {
                $rappel = new Rappel();
                $rappel->setMontantEmis(mt_rand(0,5));
                $rappel->setDateCreation($dateCreation);
                $facture->addRappel($rappel);
            }
        }
        return $facture;
    }

    private function getRandomContact($canBeNull = false)
    {
        $contact = new Contact();

        $adresse = $this->getRandomAdresse();

        $contact ->setAdresse($adresse);

        for($n = 0; $n < mt_rand(0,3); $n++) {
            $email = $this->getRandomEmail();
            $contact->addEmail($email);
        }

        for($n = 0; $n < mt_rand(0,3); $n++) {
            $tel = $this->getRandomTelephone();
            $contact->addTelephone($tel);
        }


        if($canBeNull)
            return (rand(0,1) == 1) ? $contact : null;
        else
            return $contact;

    }

    private function getRandomEmail($canBeNull = false)
    {
        $email = new \AppBundle\Entity\Email();
        $email->setEmail($this->getAdresseEmail(false));
        $email->setExpediable((mt_rand(0,1) == 0) ? true : false );
        $email->setRemarques($this->getText(10,true));

        if($canBeNull)
            return (rand(0,1) == 1) ? $email : null;
        else
            return $email;
    }

    private function getRandomTelephone($canBeNull = false)
    {
        $tel = new Telephone();
        $tel->setRemarques($this->getText(10,true));
        $tel->setNumero($this->getPhone());

        if($canBeNull)
            return (rand(0,1) == 1) ? $tel : null;
        else
            return $tel;
    }

    /**
     *
     */
    protected function createMembers()
    {

        $progress = new ProgressBar($this->output->getOutput(), $this->numberOfMember);

        $progress->start();

        for ($i = 0; $i < $this->numberOfMember; $i++) {

            /*
             * En premier lieu, on crée une nouvelle famille. Dans cette famille, on va ajouter entre 1 et 4 gosse,
             * et entre 1 et 2 parents. Lorsqu'on a choisit le nombre de gosses à ajouter, on va incrémenter le nombre
             * de membres souhaité en tout pour que ce soit pris en compte
             */
            $famille = new Famille();
            $famille->setNom($this->getNom());
            $famille->setContact($this->getRandomContact());
            $famille->setValidity(mt_rand(0, 2));


            //Ajout des parents
            switch (mt_rand(0, 2) == 0) {
                case 0:
                    //On lui file une mère
                    $famille->setMere($this->getRandomMere());
                    break;
                case 1:
                    //On lui file un père
                    $famille->setPere($this->getRandomPere());
                    break;
                case 2:
                    //on donne les deux parent
                    $famille->setMere($this->getRandomMere());
                    $famille->setPere($this->getRandomPere());
                    break;
            }


            /*
             * Après avoir géré les parents, on va gérer les membres ainsi que leurs attributions respectives
             * afin qu'ils soient placés dans des groupes de manière efficace
             */
            $nbrDeGosses = 0;

            if (($this->numberOfMember - $i) < 5)
                $nbrDeGosses = $this->numberOfMember - $i;

            else
                $nbrDeGosses = mt_rand(1, 5);

            $i += $nbrDeGosses;

            for ($j = 0; $j < $nbrDeGosses; $j++) {

                $membre = $this->getRandomMember();

                $this->addRandomAttribution($membre);

                for ($k = 0; $k < mt_rand(0, 3); $k++)
                    $membre->addDistinction($this->getRandomDistinction());


                //ajout créance et facture
                $debiteurM = new DebiteurMembre();
                $membre->setDebiteur($debiteurM);
                $nbCreanceEnAttente = mt_rand(1, 3);
                for ($n = 0; $n < $nbCreanceEnAttente; $n++) {
                    $membre->getDebiteur()->addCreance($this->getCreance($membre->getDebiteur()));
                }
                $nbFacture = mt_rand(1, 3);
                for ($n = 0; $n < $nbFacture; $n++) {
                    $membre->getDebiteur()->addFacture($this->getFacture($membre->getDebiteur()));
                }

                //ajout d'envois

                $receiver = new ReceiverMembre();
                $pmail = new Mail();
                $pmail->setTitle('Envoi par poste');
                $pmail->setSender($membre->getSender());
                $receiver->addMail($pmail);
                $email = new Mail();
                $email->setTitle('Envoi par e-mail');
                $email->setSender($membre->getSender());
                $receiver->addMail($email);
                $this->em->persist($receiver);
                $membre->setReceiver($receiver);


                $famille->addMembre($membre);
            }

            //ajout créance et facture

            $debiteur = new DebiteurFamille();
            $famille->setDebiteur($debiteur);
            $nbCreanceEnAttente = mt_rand(1, 3);
            for ($n = 0; $n < $nbCreanceEnAttente; $n++) {
                $famille->getDebiteur()->addCreance($this->getCreance($famille->getDebiteur()));
            }
            $nbFacture = mt_rand(1, 3);
            for ($n = 0; $n < $nbFacture; $n++) {
                $famille->getDebiteur()->addFacture($this->getFacture($famille->getDebiteur()));
            }

            //ajout d'envois

            $receiver = new ReceiverFamille();
            $pmail = new Mail();
            $pmail->setTitle('Envoi par poste');
            $receiver->addMail($pmail);
            $email = new Mail();
            $email->setTitle('Envoi par e-mail');
            $receiver->addMail($email);
            $this->em->persist($receiver);
            $famille->setReceiver($receiver);


            $this->em->persist($famille);

            $progress->advance();
        }

        $this->em->flush();

        $progress->finish();
    }

}