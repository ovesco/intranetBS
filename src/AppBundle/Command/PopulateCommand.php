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
use Interne\FinancesBundle\Entity\FactureToMembre;
use Interne\FinancesBundle\Entity\FactureToFamille;
use Interne\FinancesBundle\Entity\CreanceToMembre;
use Interne\FinancesBundle\Entity\CreanceToFamille;
use AppBundle\Entity\Fonction;
use AppBundle\Entity\Distinction;
use AppBundle\Entity\Type;
use AppBundle\Entity\Groupe;
use Interne\FinancesBundle\Entity\Rappel;

use Interne\SecurityBundle\Entity\Role;
use Interne\SecurityBundle\Entity\User;

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
            ->addArgument('action', InputArgument::REQUIRED, 'Quel action souhaitez-vous faire? create: crée l\'arboresance / fill: remplir la base de donnée ')
            ->addArgument('members', InputArgument::OPTIONAL, 'Combien de membres souhaitez-vous génerer ?')            //nombre de membres souhaité
            ->addArgument('fonction', InputArgument::OPTIONAL, 'Abreviation de la fonction des attributions génerées')  //Abbreviation de la fonction des attributions souhaitées
            ->addArgument('type', InputArgument::OPTIONAL, 'ID du type des groupes des attributions génerées')          //ID du type de groupe souhaité
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        /** @var \Doctrine\ORM\EntityManager $em */
        $em                           = $this->getContainer()->get('doctrine.orm.entity_manager');
        $action                       = $input->getArgument('action');
        $nombreDeMembresSouhaites     = intval($input->getArgument('members'));
        $fonction                     = $input->getArgument('fonction');
        $type                         = $input->getArgument('type');


        if($action == 'create'){

            //on enregistre la liste des distinctions
            $distinctions = $this->getDistinctions();
            foreach($distinctions as $dist)
            {
                $distinctionInBDD = $em->getRepository('AppBundle:Distinction')->findOneByNom($dist);
                if($distinctionInBDD == null)
                {
                    //création si inexistant
                    $new = new Distinction();
                    $new->setNom($dist);
                    $em->persist($new);
                }
            }

            //on energistre la liste des fonctions
            $fonctions = $this->getFonctions();
            foreach($fonctions as $fonc => $abrev)
            {
                $fonctionInBDD = $em->getRepository('AppBundle:Fonction')->findOneByNom($fonc);
                if($fonctionInBDD == null)
                {
                    //création si inexistant
                    $new = new Fonction();
                    $new->setNom($fonc);
                    $new->setAbreviation($abrev);
                    $em->persist($new);
                }
            }

            //On crée la hierarchie!!!
            $em->flush();
            $groupes = $this->getGroupes();
            $this->createHierarchie($em,null,$groupes);

            //Sauvegarde complète
            $em->flush();




        }
        elseif($action == 'fill'){

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
                $famille->setNom($this->getNom());
                $famille->setAdresse($this->getRandomAdresse(true));
                $famille->setValidity(mt_rand(0,2));
                $famille->setTelephone($this->getPhone(true));
                $famille->setEmail($this->getEmail(true));


                //Ajout des parents
                switch(mt_rand(0,2) == 0){
                    case 0:
                        //On lui file une mère
                        $famille->setMere($this->getRandomGeniteur('f'));
                        break;
                    case 1:
                        //On lui file un père
                        $famille->setPere($this->getRandomGeniteur('m'));
                        break;
                    case 2:
                        //on donne les deux parent
                        $famille->setMere($this->getRandomGeniteur('f'));
                        $famille->setPere($this->getRandomGeniteur('m'));
                        break;
                }


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


                    //ajout créance et facture
                    $nbCreanceEnAttente = mt_rand(1,3);
                    for($n = 0; $n < $nbCreanceEnAttente; $n++) {
                        $membre->addCreance($this->getCreance($membre));
                    }
                    $nbFacture = mt_rand(1,3);
                    for($n = 0; $n < $nbFacture; $n++) {
                        $membre->addFacture($this->getFacture($membre));
                    }

                    $famille->addMembre($membre);
                }

                //ajout créance et facture
                $nbCreanceEnAttente = mt_rand(1,3);
                for($n = 0; $n < $nbCreanceEnAttente; $n++) {
                    $famille->addCreance($this->getCreance($famille));
                }
                $nbFacture = mt_rand(1,3);
                for($n = 0; $n < $nbFacture; $n++) {
                    $famille->addFacture($this->getFacture($famille));
                }




                $em->persist($famille);

                $progress->advance();
            }

            $em->flush();

            $progress->finish();
        }
        elseif($action == 'security')
        {
            $membre = new Membre();
            $role = new Role();
            $user = new User();

            $membre->setPrenom('Security user');
            $membre->setSexe('m');
            $membre->setValidity(0);

            $user->setMembre($membre);
            $user->setPassword('swag');
            $user->setUsername('yolo');

            $role->setName('user');
            $role->setRole('ROLE_USER');

            $role->addUser($user);
            $user->addRole($role);

            $em->persist($membre);
            $em->persist($user);
            $em->persist($role);
            $em->flush();



        }


    }

    /**
     * Cette fonction crée la hierarchie des groupes définit dans la fonction getGroupes.
     * Elle doit être appelée après la création des fonctions et des disintinctions.
     *
     * @param $em
     * @param $parent
     * @param $childsGroupes
     */
    private function createHierarchie($em,$parent,$childsGroupes){

        foreach($childsGroupes as $name => $groupeData)
        {

            $groupe = $em->getRepository('AppBundle:Groupe')->findOneByNom($name);

            if($groupe == null)
            {
                //création si inexistant
                $groupe = new Groupe();
                $groupe->setNom($name);
            }

            $type = $em->getRepository('AppBundle:Type')->findOneBy(array('nom'=>$groupeData[0]));

            if($type == null)
            {
                //création si inexistant
                $type = new Type();
                $type->setNom($groupeData[0]);
                $type->setAffichageEffectifs(true);
            }

            //forcément déjà existant car la création des fonctions et faite avant!
            $fonctionChef = $em->getRepository('AppBundle:Fonction')->findOneBy(array('abreviation'=>$groupeData[1]));

            $type->setFonctionChef($fonctionChef);

            $groupe->setType($type);
            $groupe->setParent($parent);
            $groupe->setActive(true);

            //next sub level
            $childs = $groupeData[2];
            $this->createHierarchie($em,$groupe,$childs);

            $em->persist($type);
            $em->persist($groupe);
            $em->flush();

        }

    }

    private function getGroupes(){

        $groupes = array(
            'Brigade de Sauvabelin' => array(
                'Brigade','CDT', array(
                    'Eclaireurs' => array(
                        'Branche','CB', array(
                            'Berisal'=> array(
                                'Troupe','CT', array(
                                    'Faucons'=> array(
                                        'Patrouille','CP', array()
                                    ),
                                    'Cerfs'=> array(
                                        'Patrouille','CP', array(),
                                    ),
                                    'Panthère'=> array(
                                        'Patrouille','CP', array(),
                                    ),
                                ),
                            ),
                            'Montfort'=> array(
                                'Troupe','CT', array(
                                    'Fregate'=> array(
                                        'Patrouille','CP', array()
                                    ),
                                    'Optimiste'=> array(
                                        'Patrouille','CP', array(),
                                    ),
                                    'Galion'=> array(
                                        'Patrouille','CP', array(),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'Eclaireuses' => array(
                        'Branche','CB', array(
                            'Solalex'=> array(
                                'Troupe','CT', array(
                                    'Hirondelles'=> array(
                                        'Patrouille','CP', array()
                                    ),
                                    'Daufins'=> array(
                                        'Patrouille','CP', array(),
                                    ),
                                    'Tigresses'=> array(
                                        'Patrouille','CP', array(),
                                    ),
                                ),
                            ),
                        ),
                    )
                )
            ),
            'ADABS' => array(
                'Anciens','Pres.', array()
            )
        );

        return $groupes;

    }

    private function getDistinctions(){
        return array(
            'Cravate Bleu',
            '1er classe',
            'Badge feu',
            'Aspirant',

        );
    }

    private function getFonctions(){
        return array(
            'Commandant'=>'CDT',
            'Chef de Branche'=>'CB',
            'Chef de Troupe'=>'CT',
            'Chef de Patrouille'=>'CP',
            'Chef de Meute'=>'CM',
            'Chef Louvetaux'=>'CL',
            'Adjoint'=>'Adj',
            'Eclaireur'=>'Ecl',
            'Louveteaux'=>'Lvtx',
            'Président ADABS'=>'Pres.'


        );
    }

    /**
     * Génère une adresse bidon
     * @param $canBeNull
     * @return Adresse
     */
    private function getRandomAdresse($canBeNull = false) {

        $faker   = \Faker\Factory::create('fr_FR');
        $adresse = new Adresse();

        $adresse->setLocalite($faker->city);
        $adresse->setNpa($faker->postcode);
        $adresse->setRue($faker->streetName . ' ' . $faker->randomDigitNotNull);
        $adresse->setRemarques($this->getText(100,true));
        $adresse->setEmail($this->getEmail(true));
        $adresse->setAdressable( (mt_rand(0,1) == 0) ? true : false );
        $adresse->setValidity( (mt_rand(0,1) == 0) ? true : false );
        $adresse->setTelephone($this->getPhone(true));
        $adresse->setMethodeEnvoi((mt_rand(0,1) == 0) ? 'Email' : 'Courrier');

        if($canBeNull)
            return (rand(0,1) == 1) ? $adresse : null;
        else
            return $adresse;
    }

    /**
     * Retourne un géniteur aléatoire
     * @param string $sexe le sexe du géniteur
     * @return Geniteur
     */
    private function getRandomGeniteur($sexe) {

        $geniteur = new Geniteur();

        $geniteur->setPrenom($this->getPrenom($sexe));
        $geniteur->setEmail($this->getEmail(true));
        $geniteur->getProfession($this->getProfession(true));
        $geniteur->setAdresse($this->getRandomAdresse(true));
        $geniteur->setSexe($sexe);
        $geniteur->setTelephone($this->getPhone(true));
        $geniteur->setIban($this->getIban(true));

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
        $membre->setPrenom($this->getPrenom($sexe));
        $membre->setEmail($this->getEmail(true));
        $membre->setAdresse($this->getRandomAdresse(true));
        $membre->setNaissance($this->getRandomDateNaissance());
        $membre->setInscription($this->getRandomInscription());
        $membre->setValidity(mt_rand(0,2));
        $membre->setTelephone($this->getPhone(true));
        $membre->setNumeroAvs(mt_rand(111111111,999999999));
        $membre->setNumeroBs(mt_rand(0, 99999));
        $membre->setStatut($this->getStatut());
        $membre->setIban($this->getIban(true));

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

        if($sexe == 'f')
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

        $frFaker = \Faker\Factory::create('fr_FR');

        if($canBeNull)
            return (rand(0,1) == 1) ? $frFaker->phoneNumber : null;
        else
            return $frFaker->phoneNumber;

    }

    /**
     * @param bool $canBeNull
     * @return null|string
     */
    private function getEmail($canBeNull = false){

        $frFaker = \Faker\Factory::create('fr_FR');

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
        $faker   = \Faker\Factory::create('fr_FR');
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
     * @param $owner
     * @param bool $payee
     * @return Creance
     */
    private function getCreance($owner, $payee = false){

        $creance = null;

        if($owner->isClass('Membre'))
        {
            $creance = new CreanceToMembre();
            $creance->setMembre($owner);
        }
        if($owner->isClass('Famille'))
        {
            $creance = new CreanceToFamille();
            $creance->setFamille($owner);
        }

        $annee = mt_rand(2000,2015);
        $periode = array('hiver','printemps','été','automne');

        $creance->setTitre((mt_rand(0,1) == 0) ? 'Cotisation '.$annee : 'Camp '.$periode[mt_rand(0,3)].' '.$annee);
        $creance->setRemarque($this->getText(120,true));
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
     * @param $owner
     * @return Facture
     */
    private function getFacture($owner){


        $facture = null;

        if($owner->isClass('Membre'))
        {
            $facture = new FactureToMembre();
            $facture->setMembre($owner);
        }
        if($owner->isClass('Famille'))
        {
            $facture = new FactureToFamille();
            $facture->setFamille($owner);
        }

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
                $facture->addCreance($this->getCreance($owner,true));
            }
            $facture->setStatut('payee');
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
                $facture->addCreance($this->getCreance($owner));
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

}