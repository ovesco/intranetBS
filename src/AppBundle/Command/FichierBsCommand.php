<?php

namespace AppBundle\Command;

use AppBundle\Entity\Adresse;
use AppBundle\Entity\Attribution;
use AppBundle\Entity\Distinction;
use AppBundle\Entity\Fonction;
use AppBundle\Entity\Geniteur;
use AppBundle\Entity\Groupe;
use AppBundle\Entity\Famille;
use AppBundle\Entity\Membre;
use AppBundle\Entity\ObtentionDistinction;
use AppBundle\Entity\Type;
use ClassesWithParents\F;
use Interne\FinancesBundle\Entity\CreanceToMembre;
use Interne\FinancesBundle\Entity\CreanceToFamille;
use Interne\FinancesBundle\Entity\FactureToFamille;
use Interne\FinancesBundle\Entity\FactureToMembre;
use Interne\FinancesBundle\Entity\Rappel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\Query\ResultSetMapping;



class FichierBsCommand extends ContainerAwareCommand
{
    private $em;
    private $fichierBsEm;
    private $connectionFichierBs;
    private $linkTables;

    protected function configure()
    {
        $this
            ->setName('app:fichier')
            ->setDescription('Remplir la base de donnée')
            ->addArgument('action', InputArgument::REQUIRED, 'Quel action souhaitez-vous faire?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        /** @var \Doctrine\ORM\EntityManager $em */
        $em                           = $this->getContainer()->get('doctrine.orm.entity_manager');
        $fichierEm                    = $this->getContainer()->get('doctrine')->getManager('fichier');
        $action                       = $input->getArgument('action');

        $this->em = $em;
        $this->fichierBsEm = $fichierEm;
        $this->connectionFichierBs = $this->fichierBsEm->getConnection();

        $this->linkTables = array('link_fonction',
            'link_distinction','link_famille',
            'link_membre','link_groupe_racine',
            'link_groupe_branche','link_groupe_unite',
            'link_membre_distinctions','link_facture',
            'link_attribution_membre'
        );

        if($action == 'reset') {
            /*
             * A utiliser lorsqu'on veut recommancer. (supprime les tables de lien)
             */
            foreach($this->linkTables as $table)
                $this->deleteLinkTable($table);
        }
        elseif($action == 'prepare') {
            /*
             * A utiliser avec l'action "load" (cree les tables de lien)
             */
            foreach($this->linkTables as $table)
                $this->createLinkTable($table);
        }
        elseif($action == 'load'){

            ini_set('memory_limit', '-1'); //avoid memory limit exception!!!

            /*
             * Chargement des attributions (fonction dans AppBundle)
             */
            $sql = 'SELECT attributions.id_attribution, attributions.nom_attribution, attributions.abrev_attribution FROM attributions';
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('id_attribution', 'id');
            $rsm->addScalarResult('nom_attribution', 'nom');
            $rsm->addScalarResult('abrev_attribution', 'abreviation');
            $results = $fichierEm->createNativeQuery($sql,$rsm)->getResult();

            /*
             * Enregistrement des fonctions
             */
            foreach($results as $result)
            {
                if(!$this->isAlreadySet('link_fonction',$result['id']))
                {
                    //création de la fonction
                    $fonction = new Fonction(utf8_encode($result['nom']),utf8_encode($result['abreviation']));
                    $em->persist($fonction);
                    $em->flush();

                    //on sauve le lien
                    $this->setLink('link_fonction',$fonction->getId(),$result['id']);

                    //info
                    //echo 'Fonction id:'. $result['id'].' (new id: '.$fonction->getId().') => done',PHP_EOL;
                }
            }
            echo 'Chargement des Attributions (fonction dans AppBundle) => ok',PHP_EOL;

            /*
             * On crée la hierachie des groupes pour commencer. En sortant les fichiers.
             */
            $sql = 'SELECT fichiers.id_fichier, fichiers.nom_fichier FROM fichiers';
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('id_fichier', 'id');
            $rsm->addScalarResult('nom_fichier', 'nom');
            $results = $fichierEm->createNativeQuery($sql,$rsm)->getResult();


            /*
             * On charge les fichiers comme groupe principaux
             */
            foreach($results as $result)
            {
                if(!$this->isAlreadySet('link_groupe_racine',$result['id']))
                {
                    $fonction = new Fonction('A mofifier','A modifier');
                    $type = new Type();
                    $type->setNom('A modifier');
                    $type->setFonctionChef($fonction);
                    $type->setAffichageEffectifs(true);
                    $em->persist($fonction);
                    $em->persist($type);

                    $groupe = new Groupe();
                    $groupe->setNom($result['nom']);
                    $groupe->setActive(true);
                    $groupe->setParent(null);//groupe racine
                    $groupe->setType($type);

                    $em->persist($groupe);
                    $em->flush();

                    //on sauve le lien
                    $this->setLink('link_groupe_racine',$groupe->getId(),$result['id']);
                }

            }


            echo 'Chargement des groupe racines => ok',PHP_EOL;


            /*
             * On charge les branches
             */
            $sql = 'SELECT branches.id_branche, branches.nom_branche FROM branches';
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('id_branche', 'id');
            $rsm->addScalarResult('nom_branche', 'nom');
            $results = $fichierEm->createNativeQuery($sql,$rsm)->getResult();

            /*
             * Recupération de la branche si déjà existante
             */
            $type = null;
            $type = $em->getRepository('AppBundle:Type')->findOneBy(array('nom'=>'Branche'));

            if($type == null)
            {
                /*
                 * Creation du type Branche
                 */
                $fonction = new Fonction('Chef de branche','CB');
                $type = new Type();
                $type->setNom('Branche');
                $type->setFonctionChef($fonction);
                $type->setAffichageEffectifs(true);
                $em->persist($fonction);
                $em->persist($type);
            }


            /*
             * Enregistrement des branche dans la bs
             */
            foreach($results as $result)
            {
                if(!$this->isAlreadySet('link_groupe_branche',$result['id']))
                {
                    $newId = $this->getByOld('link_groupe_racine',1);//groupe racine BS
                    $groupeRacine = $em->getRepository('AppBundle:Groupe')->find($newId);

                    $groupe = new Groupe();
                    $groupe->setNom(utf8_encode($result['nom']));
                    $groupe->setActive(true);
                    $groupe->setParent($groupeRacine);//groupe racine BS
                    $groupe->setType($type);
                    $em->persist($groupe);
                    $em->flush();

                    //on sauve le lien
                    $this->setLink('link_groupe_branche',$groupe->getId(),$result['id']);
                }

            }


            echo 'Chargement des Branches => ok',PHP_EOL;

            /*
             * Chargement des unités
             */
            $sql = 'SELECT unites.id_unite, unites.nom_unite, unites.id_branche FROM unites';
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('id_unite', 'id');
            $rsm->addScalarResult('nom_unite', 'nom');
            $rsm->addScalarResult('id_branche', 'branche');
            $results = $fichierEm->createNativeQuery($sql,$rsm)->getResult();


            //Récupération de l'unité si existante
            $type = null;
            $type = $em->getRepository('AppBundle:Type')->findOneBy(array('nom'=>'Unité'));

            if($type == null)
            {
                /*
                 * Creation du type Unité
                 */
                $fonction = new Fonction('Chef d\'unité','CU');
                $type = new Type();
                $type->setNom('Unité');
                $type->setFonctionChef($fonction);
                $type->setAffichageEffectifs(true);
                $em->persist($fonction);
                $em->persist($type);
            }


            /*
             * Enregistrement des unité dans leur branche
             */
            foreach($results as $result)
            {
                if(!$this->isAlreadySet('link_groupe_unite',$result['id']))
                {
                    $newId = $this->getByOld('link_groupe_branche',$result['branche']);
                    $branche = $em->getRepository('AppBundle:Groupe')->find($newId);

                    $groupe = new Groupe();
                    $groupe->setNom(utf8_encode($result['nom']));
                    $groupe->setActive(true);
                    $groupe->setParent($branche);
                    $groupe->setType($type);
                    $em->persist($groupe);
                    $em->flush();

                    //on sauve le lien
                    $this->setLink('link_groupe_unite',$groupe->getId(),$result['id']);
                }

            }


            echo 'Chargement des unités => ok',PHP_EOL;

            /*
             * Chargement des distinctions
             */
            $sql = 'SELECT distinctions.id_distinction, distinctions.nom_distinction, distinctions.remarques_distinction FROM distinctions';
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('id_distinction', 'id');
            $rsm->addScalarResult('nom_distinction', 'nom');
            $rsm->addScalarResult('remarques_distinction', 'remarque');
            $results = $fichierEm->createNativeQuery($sql,$rsm)->getResult();

            /*
             * Enregistrement des distinctions
             */
            foreach($results as $result)
            {
                if(!$this->isAlreadySet('link_distinction',$result['id']))
                {
                    $distinction = new Distinction();
                    $distinction->setNom(utf8_encode($result['nom']));
                    $distinction->setRemarques(utf8_encode($result['remarque']));
                    $em->persist($distinction);
                    $em->flush();

                    //on sauve le lien
                    $this->setLink('link_distinction',$distinction->getId(),$result['id']);

                    //info
                    echo '.';
                    //echo 'Distinction id:'. $result['id'].' (new id: '.$distinction->getId().') => done',PHP_EOL;
                }
            }
            echo PHP_EOL,'Chargement des distinctions => ok',PHP_EOL;

            /*
             * Chargement des familles
             */
            $start = microtime(true);
            $sql = 'SELECT familles.id_famille, familles.nom_famille, familles.adresse_famille,familles.npa_famille,familles.ville_famille FROM familles';
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('id_famille', 'id');
            $rsm->addScalarResult('nom_famille', 'nom');
            $rsm->addScalarResult('adresse_famille', 'rue');
            $rsm->addScalarResult('npa_famille', 'npa');
            $rsm->addScalarResult('ville_famille', 'ville');
            $results = $fichierEm->createNativeQuery($sql,$rsm)->getResult();

            /*
             * Enregistrement des familles
             */
            foreach($results as $result)
            {
                try{
                    if(!$this->isAlreadySet('link_famille',$result['id']))
                    {
                        $adresse = new Adresse();
                        $adresse->setRue(utf8_encode($result['rue']));
                        $adresse->setNpa(utf8_encode($result['npa']));
                        $adresse->setLocalite(utf8_encode($result['ville']));
                        $adresse->setAdressable(true);
                        $adresse->setValidity(true);
                        $adresse->setMethodeEnvoi('Courrier');

                        $famille = new Famille();
                        $famille->setNom(utf8_encode($result['nom']));
                        $famille->setValidity(0);
                        $famille->setAdresse($adresse);

                        $em->persist($adresse);
                        $em->persist($famille);
                        $em->flush();

                        //on sauve le lien
                        $this->setLink('link_famille',$famille->getId(),$result['id']);
                        echo '.';
                    }


                }catch (\Exception $e){
                    echo 'Erreur: '.$e->getMessage(),' => Famille id_old:'.$result['id'],PHP_EOL;
                }

            }
            $executionTime = (microtime(true) - $start);
            echo PHP_EOL,'Chargement des familles => ok => exexution: '.$executionTime.'[s]',PHP_EOL;


            /*
             * Chargement des membres
             */

            $sql = 'SELECT
                        membres.id_membre,
                        membres.id_famille,
                        membres.id_fichier,
                        membres.nom,
                        membres.no_membre,
                        membres.prenom,
                        membres.sexe,
                        membres.nom_pere,
                        membres.prenom_pere,
                        membres.profession_pere,
                        membres.nom_mere,
                        membres.prenom_mere,
                        membres.profession_mere,
                        membres.adresse,
                        membres.npa,
                        membres.ville,
                        membres.no_avs,
                        membres.date_naissance,
                        membres.tel,
                        membres.natel,
                        membres.email,
                        membres.remarques_membre
                    FROM
                        membres
                    ORDER BY
                        membres.id_membre ASC';
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('id_membre', 'id');
            $rsm->addScalarResult('id_famille', 'famille');
            $rsm->addScalarResult('id_fichier', 'fichier');
            $rsm->addScalarResult('nom', 'nom');
            $rsm->addScalarResult('no_membre', 'numeroBS');
            $rsm->addScalarResult('prenom', 'prenom');
            $rsm->addScalarResult('sexe', 'sexe');
            $rsm->addScalarResult('nom_pere', 'nom_pere');
            $rsm->addScalarResult('prenom_pere', 'prenom_pere');
            $rsm->addScalarResult('profession_pere', 'profession_pere');
            $rsm->addScalarResult('nom_mere', 'nom_mere');
            $rsm->addScalarResult('prenom_mere', 'prenom_mere');
            $rsm->addScalarResult('profession_mere', 'profession_mere');
            $rsm->addScalarResult('adresse', 'rue');
            $rsm->addScalarResult('npa', 'npa');
            $rsm->addScalarResult('ville', 'ville');
            $rsm->addScalarResult('no_avs', 'avs');
            $rsm->addScalarResult('date_naissance', 'naissance');
            $rsm->addScalarResult('tel', 'tel');
            $rsm->addScalarResult('natel', 'natel');
            $rsm->addScalarResult('email', 'email');
            $rsm->addScalarResult('remarques_membres', 'remarque');


            $results = $fichierEm->createNativeQuery($sql,$rsm)->getResult();


            /*
             * Enregistrement des membres
             */
            $start = microtime(true);
            foreach($results as $result)
            {
                try
                {
                    if(!$this->isAlreadySet('link_membre',$result['id']))
                    {

                        $membre = new Membre();
                        $membre->setValidity(0);
                        $membre->setNaissance(new \DateTime($result['naissance']));

                        /*
                         * Création des géniteurs
                         */
                        $pere = null;
                        $mere = null;
                        if($result['prenom_pere'] != null)
                        {
                            $pere = new Geniteur();
                            $pere->setNom(utf8_encode($result['nom_pere']));
                            $pere->setPrenom(utf8_encode($result['prenom_pere']));
                            $pere->setSexe('m');
                            $pere->setProfession(utf8_encode($result['profession_pere']));
                        }
                        if($result['prenom_mere'] != null)
                        {
                            $mere = new Geniteur();
                            $mere->setNom(utf8_encode($result['nom_mere']));
                            $mere->setPrenom(utf8_encode($result['prenom_mere']));
                            $mere->setSexe('f');
                            $mere->setProfession(utf8_encode($result['profession_mere']));
                        }



                        /*
                         * Lien avec la famille
                         */
                        $famille = null;
                        if($result['famille'] == 0) //0 = sans famille dans l'ancien fichier
                        {
                            $famille = new Famille(); //donc nouvelle famille
                            $famille->setNom(utf8_encode($result['nom']));
                            $famille->setValidity(0);

                            // on sauve les géniteurs si ils ont été crée
                            if($pere != null)
                            {
                                $famille->setPere($pere);
                                $this->em->persist($pere);
                            }
                            if($mere != null)
                            {
                                $famille->setMere($mere);
                                $this->em->persist($mere);
                            }


                        }
                        else
                        {
                            $newId = $this->getByOld('link_famille',$result['famille']);

                            $famille = $em->getRepository('AppBundle:Famille')->find($newId);

                            //si on ne trouve pas la famille
                            if($famille == null)
                            {
                                // il y a une erreur. On crée une nouvelle famille.
                                $famille = new Famille(); //donc nouvelle famille
                                $famille->setNom(utf8_encode($result['nom']));
                                $famille->setValidity(0);

                                array_push($errorReport, 'La famille avec l\'index '.$result['famille'].
                                    ' n\'a pas été trouvée. Une famille à été crée en'.
                                    'complément pour le membre: '.$result['nom'].' '.$result['prenom']);

                            }


                            /*
                             * Si les géniteurs était inexistant.
                             */
                            if(($famille->getPere() == null)&&($pere != null))
                            {
                                $famille->setPere($pere);
                                $this->em->persist($pere);
                            }
                            if(($famille->getMere() == null)&&($mere != null))
                            {
                                $famille->setMere($mere);
                                $this->em->persist($mere);
                            }

                        }


                        //on crée le lien
                        $famille->addMembre($membre);

                        $this->em->persist($famille);


                        //On se fiche un peu de cette info...
                        switch($result['fichier']){
                            case 1: // à la BS
                                break;
                            case 2: // à l'adabs
                                break;
                            case 3: // inscrit
                                break;
                            case 4: // désincrit
                                break;
                            case 5: // personne décédée
                                $membre->setRemarques('Décédé');
                                break;
                        }

                        $membre->setNumeroBs($result['numeroBS']);
                        $membre->setPrenom(utf8_encode($result['prenom']));
                        $membre->setSexe($result['sexe']);



                        $adresse = new Adresse();
                        $adresse->setRue(utf8_encode($result['rue']));
                        $adresse->setNpa(utf8_encode($result['npa']));
                        $adresse->setLocalite(utf8_encode($result['ville']));

                        $adresse->setEmail(utf8_encode($result['email']));

                        //on sauve le téléphone avec une priorité sur les numéros de natel
                        if($result['tel'] != null)
                            $adresse->setTelephone(utf8_encode($result['tel']));
                        elseif($result['natel'] != null)
                            $adresse->setTelephone(utf8_encode($result['natel']));

                        //propriétés par défaut
                        $adresse->setAdressable(true);
                        $adresse->setValidity(true);
                        $adresse->setMethodeEnvoi('Courrier');

                        //on ajoute l'adresse au membre
                        $membre->setAdresse($adresse);



                        $this->em->persist($membre);
                        $this->em->persist($adresse);
                        $this->em->flush();



                        $this->setLink('link_membre',$membre->getId(),$result['id']);

                        if(($membre->getId()%100) == 0) {
                            $executionTime = (microtime(true) - $start);
                            $start = microtime(true);
                            echo PHP_EOL, $membre->getId() . ': 100 membres ajouté => exexution: ' . $executionTime . '[s]', PHP_EOL;
                        }



                        //petite amélioration pour la mémoire.
                        $this->em->clear();
                        gc_collect_cycles();




                        echo '.';
                    }






                }
                catch(\Exception $e)
                {
                    echo PHP_EOL,'Erreur: '.$e->getMessage().' => Membre id_old:'.$result['id'],PHP_EOL;
                }
            }

            echo PHP_EOL,'Chargement des membre => ok',PHP_EOL;


            /*
             * Chargement du lien distinction/membre
             */
            $sql = 'SELECT
                        membres_distinctions.id_membres_distinction,
                        membres_distinctions.id_distinction,
                        membres_distinctions.id_membre,
                        membres_distinctions.date_membres_distinction
                    FROM membres_distinctions';
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('id_membres_distinction', 'id');
            $rsm->addScalarResult('id_distinction', 'id_distinction');
            $rsm->addScalarResult('id_membre', 'id_membre');
            $rsm->addScalarResult('date_membres_distinction', 'date');
            $results = $fichierEm->createNativeQuery($sql,$rsm)->getResult();

            /*
             * Enregistrement des distinctions/membres
             */
            $start = microtime(true);
            foreach($results as $result)
            {
                try{

                    if(!$this->isAlreadySet('link_membre_distinctions',$result['id']))
                    {
                        $newId = $this->getByOld('link_membre',$result['id_membre']);
                        $membre = $em->getRepository('AppBundle:Membre')->find($newId);

                        $newId = $this->getByOld('link_distinction',$result['id_distinction']);
                        $distinction = $em->getRepository('AppBundle:Distinction')->find($newId);

                        $link = new ObtentionDistinction();
                        $link->setObtention(new \DateTime($result['date']));
                        $link->setDistinction($distinction);
                        $link->setMembre($membre);
                        $em->persist($link);
                        $em->flush();

                        $this->setLink('link_membre_distinctions',$link->getId(),$result['id']);

                        echo '.';

                        if(($link->getId()%100) == 0)
                        {
                            $executionTime = (microtime(true) - $start);
                            $start = microtime(true);
                            echo PHP_EOL,'100 distinction ajouté => exexution: '.$executionTime.'[s]',PHP_EOL;
                        }
                    }



                }
                catch(\Exception $e)
                {
                    echo PHP_EOL,'Erreur: '.$e->getMessage(),' => obtentionDistinction id_old:'.$result['id'],PHP_EOL;
                }
            }


            echo PHP_EOL,'Chargement des distinctions <=> membres => ok',PHP_EOL;



            /*
             * Chargement du lien attribution/membre
             */
            $sql = 'SELECT
                        membres_attributions.id_membres_attribution,
                        membres_attributions.id_attribution,
                        membres_attributions.id_membre,
                        membres_attributions.id_unite,
                        membres_attributions.date_debut_membres_attribution,
                        membres_attributions.date_fin_membres_attribution,
                        membres_attributions.remarques_membres_attribution
                    FROM membres_attributions';
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('id_membres_attribution', 'id');
            $rsm->addScalarResult('id_attribution', 'id_attribution');
            $rsm->addScalarResult('id_membre', 'id_membre');
            $rsm->addScalarResult('id_unite', 'id_unite');
            $rsm->addScalarResult('date_debut_membres_attribution', 'debut');
            $rsm->addScalarResult('date_fin_membres_attribution', 'fin');
            $rsm->addScalarResult('remarques_membres_attribution', 'remarque');
            $results = $fichierEm->createNativeQuery($sql,$rsm)->getResult();


            /*
             * Reconsitution du systeme de patrouille
             *
             * On se base sur la féréquance des nom de patrouille
             * dans le champ de remarque.
             *
             */
            $occurances = array();
            foreach($results as $key => $result)
            {
                //on commence par éliminer tout les champs de remarque inutile
                $idUnite = $result['id_unite'];
                $remarque = $this->formLatin1($result['remarque']);
                if(($remarque != '') and ($remarque != '-'))
                {

                    //on rempli un tableau d'occurance
                    if(isset($occurances[$idUnite]))
                    {
                        /*
                         * On regarde si une remarque précédente possède une similarité...
                         * Si c'est le cas, on ajoute une occurance à celle ci.
                         */
                        $saved = false;
                        foreach($occurances[$idUnite] as $remarqueAlreadySaved => $nbOccurance)
                        {
                            if(!$saved)
                            {
                                similar_text($remarque,$remarqueAlreadySaved,$percent);
                                if($percent > 50) //valeur choisie...ca marche bien.
                                {
                                    $occurances[$idUnite][$remarqueAlreadySaved] = $nbOccurance + 1;
                                    $saved = true;
                                }
                            }
                        }
                        if(!$saved)
                        {
                            if(isset($occurances[$idUnite][$remarque]))
                            {
                                $occurances[$idUnite][$remarque] = $occurances[$idUnite][$remarque] + 1;
                            }
                            else
                            {
                                $occurances[$idUnite][$remarque] = 1;
                            }
                        }

                    }
                    else{
                        $occurances[$idUnite] = array();
                        $occurances[$idUnite][$remarque] = 1;
                    }
                    $results[$key]['remarque'] = $remarque;

                }
                else
                {
                    $results[$key]['remarque'] = null; // mise à nul de la remarque sans importance.
                }

            }

            /*
             * Recupération du type patrouille si déjà existante
             */
            $type = null;
            $type = $em->getRepository('AppBundle:Type')->findOneBy(array('nom'=>'Patrouille'));

            if($type == null)
            {
                /*
                 * Creation du type Branche
                 */
                $type = new Type();
                $type->setNom('Patrouille');

                $newId = $this->getByOld('link_fonction',49); //49 = index CP
                $fonction = $em->getRepository('AppBundle:Fonction')->find($newId);

                $type->setFonctionChef($fonction);
                $type->setAffichageEffectifs(true);
                $em->persist($type);
                $em->flush();
            }



            /*
             * Création des patrouilles en se basant sur les occurances.
             */
            foreach($occurances as $idUnite => $listeOccurances)
            {
                foreach($listeOccurances as $nomPatrouille => $nb)
                {
                    if($nb > 3) //si le nombre d'occurance est suffisant, on crée la patrouile
                    {
                        if($this->isAlreadySet('link_groupe_unite',$idUnite))
                        {

                            $patrouille = new Groupe();
                            $patrouille->setNom($nomPatrouille);
                            $patrouille->setType($type);
                            $patrouille->setActive(true);

                            $newId = $this->getByOld('link_groupe_unite',$idUnite); //49 = index CP
                            $groupeParent = $em->getRepository('AppBundle:Groupe')->find($newId);

                            $existingPatrouille = false;
                            foreach($groupeParent->getEnfants() as $child)
                            {
                                /*
                                 * On regarde si la patrouille existe déja.
                                 */
                                if($child->getNom() == ucwords($nomPatrouille))
                                {
                                    $existingPatrouille = true;
                                }

                            }

                            if(!$existingPatrouille)
                            {

                                $patrouille->setParent($groupeParent);
                                $em->persist($patrouille);
                                $em->flush();
                            }

                        }
                    }
                }
            }


            echo PHP_EOL,'Chargement des patrouilles => ok',PHP_EOL;

            /*
             * Chargement des attributions/membres
             */
            $start = microtime(true);
            foreach($results as $result)
            {

                try{

                    if(!$this->isAlreadySet('link_attribution_membre',$result['id']))
                    {


                        if( ($this->isAlreadySet('link_membre',$result['id_membre'])) &&
                            ($this->isAlreadySet('link_fonction',$result['id_attribution'])) &&
                            ($this->isAlreadySet('link_groupe_unite',$result['id_unite'])) )
                        {

                            $newId = $this->getByOld('link_membre',$result['id_membre']);
                            $membre = $em->getRepository('AppBundle:Membre')->find($newId);

                            $newId = $this->getByOld('link_fonction',$result['id_attribution']);
                            $fonction = $em->getRepository('AppBundle:Fonction')->find($newId);

                            $newId = $this->getByOld('link_groupe_unite',$result['id_unite']);
                            $unite = $em->getRepository('AppBundle:Groupe')->find($newId);





                            $attribution = new Attribution();

                            $attribution->setDateDebut(new \DateTime($result['debut']));
                            if($result['fin'] != '0000-00-00')
                            {
                                $attribution->setDateFin(new \DateTime($result['fin']));
                            }
                            $attribution->setFonction($fonction);
                            $attribution->setMembre($membre);

                            //TODO: le champ remarque n'est pas implémenté dans Attribution.php, on a donc une perte d'info

                            /*
                             * On met l'attribution dans le groupe mais on regarde si
                             * une patrouille correspond à la remarque.
                             */
                            $remarque = $this->formLatin1($result['remarque']);
                            $saved = false;



                            foreach($unite->getEnfants() as $patrouille)
                            {
                                similar_text(ucwords($remarque),$patrouille->getNom(),$percent);
                                if(($percent > 50)&&(!$saved))
                                {
                                    //alors c'est la bonne patrouille et on l'enregistre.
                                    $attribution->setGroupe($patrouille);
                                    $saved = true;
                                }
                            }

                            if(!$saved) //aucune patrouille trouvée
                            {
                                $attribution->setGroupe($unite);
                            }

                            $em->persist($attribution);
                            $em->flush();

                            //on sauve le lien
                            $this->setLink('link_attribution_membre',$attribution->getId(),$result['id']);

                            echo '.';
                            if(($attribution->getId()%100) == 0)
                            {
                                $executionTime = (microtime(true) - $start);
                                $start = microtime(true);
                                echo PHP_EOL,'100 attribution ajouté => exexution: '.$executionTime.'[s]',PHP_EOL;
                            }
                        }
                    }


                }catch(\Exception $e)
                {
                    echo PHP_EOL,'Erreur: '.$e->getMessage(),' => Attribution id_old:'.$result['id'],PHP_EOL;
                }
            }
            echo PHP_EOL,'Chargement des attributions => ok',PHP_EOL;


            /*
             * Chargement des factures
             */
            $sql = 'SELECT
                        factures.id_facture,
                        factures.id_membre,
                        factures.id_famille,
                        factures.nom_facture,
                        factures.montant_facture,
                        factures.montant_paye_facture,
                        factures.date_facture,
                        factures.date_paye_facture,
                        factures.date_rappel_1,
                        factures.date_rappel_2,
                        factures.status_facture,
                        factures.remarques_facture
                    FROM factures';
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('id_facture', 'id');
            $rsm->addScalarResult('id_famille', 'id_famille');
            $rsm->addScalarResult('id_membre', 'id_membre');
            $rsm->addScalarResult('nom_facture', 'nom');
            $rsm->addScalarResult('montant_facture', 'montantEmis');
            $rsm->addScalarResult('montant_paye_facture', 'montantRecu');
            $rsm->addScalarResult('remarques_facture', 'remarque');
            $rsm->addScalarResult('date_facture', 'creation');
            $rsm->addScalarResult('date_paye_facture', 'payement');
            $rsm->addScalarResult('date_rappel_1', 'date_rappel_1');
            $rsm->addScalarResult('date_rappel_2', 'date_rappel_2');
            $rsm->addScalarResult('status_facture', 'statut');
            $results = $fichierEm->createNativeQuery($sql,$rsm)->getResult();




            $start = microtime(true);
            foreach($results as $result)
            {
                try{

                    if(!$this->isAlreadySet('link_facture',$result['id']))
                    {

                        if(($result['id_famille'] != 0) or ($result['id_membre'] != 0))
                        {

                            $famille = null;
                            if($this->isAlreadySet('link_famille',$result['id_famille']))
                            {
                                $newId = $this->getByOld('link_famille', $result['id_famille']);
                                $famille = $em->getRepository('AppBundle:Famille')->find($newId);
                            }
                            $membre = null;
                            if($this->isAlreadySet('link_membre',$result['id_membre']))
                            {
                                $newId = $this->getByOld('link_membre', $result['id_membre']);
                                $membre = $em->getRepository('AppBundle:Membre')->find($newId);
                            }

                            /*
                             * On crée le lien entre les famille/membres et la facture/creance
                             */
                            $creance = null;
                            $facture = null;

                            if(($famille == null) and ($membre == null))
                            {

                                /*
                                 * si ni une famille ni un membre n'a été identifier.
                                 * On crée quand meme une facture pour en garder la trace.
                                 */
                                $creance = new CreanceToFamille();
                                $facture = new FactureToFamille();

                            }
                            if ($famille != null)
                            {

                                $creance = new CreanceToFamille();
                                $creance->setFamille($famille);

                                $facture = new FactureToFamille();
                                $facture->setFamille($famille);
                            }
                            elseif($membre != null)
                            {

                                $creance = new CreanceToMembre();
                                $creance->setMembre($membre);

                                $facture = new FactureToMembre();
                                $facture->setMembre($membre);
                            }



                            $creance->setMontantEmis($result['montantEmis']);
                            $creance->setMontantRecu($result['montantRecu']);
                            $creance->setDateCreation(new \DateTime($result['creation']));

                            /*
                             * On sauve l'ancien numéro de référance
                             */
                            $creance->setTitre('(Facture N: '.$result['id'].' ) '.utf8_encode($result['nom']));
                            $creance->setRemarque('(Facture N: '.$result['id'].' dans l\'ancien fichier) '.utf8_encode($result['remarque']));

                            $facture->setDateCreation(new \DateTime($result['creation']));



                            /*
                             * Ajout des rappels
                             */
                            if ($result['date_rappel_1'] != '0000-00-00') {
                                $rappel1 = new Rappel();
                                $rappel1->setDateCreation(new \DateTime($result['date_rappel_1']));
                                $facture->addRappel($rappel1);
                                $em->persist($rappel1);
                            }
                            if ($result['date_rappel_2'] != '0000-00-00') {
                                $rappel2 = new Rappel();
                                $rappel2->setDateCreation(new \DateTime($result['date_rappel_2']));
                                $facture->addRappel($rappel2);
                                $em->persist($rappel2);
                            }

                            /*
                             * traitement en fonction du statut.
                             */
                            switch ($result['statut']) {
                                case 'payee':
                                    $facture->setStatut('payee');
                                    $facture->setDatePayement(new \DateTime($result['payement']));
                                    break;
                                case 'rappel_1':
                                    $facture->setStatut('ouverte');
                                    break;
                                case 'rappel_2':
                                    $facture->setStatut('ouverte');
                                    break;
                                case 'non_payee':
                                    $facture->setStatut('ouverte');
                                    break;
                            }



                            $facture->addCreance($creance);
                            $em->persist($creance);
                            $em->persist($facture);
                            $em->flush();

                            //on sauve le lien
                            $this->setLink('link_facture',$facture->getId(),$result['id']);

                            echo '.';
                            if (($facture->getId() % 100) == 0) {
                                $executionTime = (microtime(true) - $start);
                                $start = microtime(true);
                                echo PHP_EOL, '100 facture ajouté => exexution: ' . $executionTime . '[s]', PHP_EOL;
                            }

                            //echo 'facture id: '.$result['id'].' (new id:'.$facture->getId().') => done',PHP_EOL;


                        }



                    }
                }
                catch(\Exception $e)
                {
                    echo PHP_EOL,'Erreur: '.$e->getMessage(),' => Facture id_old:'.$result['id'],PHP_EOL;
                }

            }

        }
        elseif($action == 'test')
        {
            /*
            * Chargement du lien attribution/membre
            */
            $sql = 'SELECT
                        membres_attributions.id_membres_attribution,
                        membres_attributions.id_attribution,
                        membres_attributions.id_membre,
                        membres_attributions.id_unite,
                        membres_attributions.date_debut_membres_attribution,
                        membres_attributions.date_fin_membres_attribution,
                        membres_attributions.remarques_membres_attribution
                    FROM membres_attributions';
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('id_membres_attribution', 'id');
            $rsm->addScalarResult('id_attribution', 'id_attribution');
            $rsm->addScalarResult('id_membre', 'id_membre');
            $rsm->addScalarResult('id_unite', 'id_unite');
            $rsm->addScalarResult('date_debut_membres_attribution', 'debut');
            $rsm->addScalarResult('date_fin_membres_attribution', 'fin');
            $rsm->addScalarResult('remarques_membres_attribution', 'remarque');
            $results = $fichierEm->createNativeQuery($sql,$rsm)->getResult();


            /*
             * Reconsitution du systeme de patrouille
             *
             * On se base sur la féréquance des nom de patrouille
             * dans le champ de remarque.
             *
             */
            $occurances = array();
            foreach($results as $key => $result)
            {
                //on commence par éliminer tout les champs de remarque inutile
                $idUnite = $result['id_unite'];

                $remarque = strtolower($result['remarque']);
                $remarque = iconv('LATIN1', 'ASCII//TRANSLIT', $remarque);
                $remarque = iconv('UTF-8', 'ASCII//IGNORE', $remarque);

                $tofind = '\'';
                $replac = '_';
                $remarque = strtr($remarque,$tofind,$replac);
                $tofind = '`';
                $replac = '_';
                $remarque = strtr($remarque,$tofind,$replac);


                if(($remarque != '') and ($remarque != '-'))
                {


                    echo $remarque,PHP_EOL;

                    //on rempli un tableau d'occurance
                    if(isset($occurances[$idUnite]))
                    {
                        /*
                         * On regarde si une remarque précédente possède une similarité...
                         * Si c'est le cas, on ajoute une occurance à celle ci.
                         */
                        $saved = false;
                        foreach($occurances[$idUnite] as $remarqueAlreadySaved => $nbOccurance)
                        {
                            if(!$saved)
                            {
                                similar_text($remarque,$remarqueAlreadySaved,$percent);
                                if($percent > 50) //valeur choisie...ca marche bien.
                                {
                                    $occurances[$idUnite][$remarqueAlreadySaved] = $nbOccurance + 1;
                                    $saved = true;
                                }
                            }
                        }
                        if(!$saved)
                        {
                            if(isset($occurances[$idUnite][$remarque]))
                            {
                                $occurances[$idUnite][$remarque] = $occurances[$idUnite][$remarque] + 1;
                            }
                            else
                            {
                                $occurances[$idUnite][$remarque] = 1;
                            }
                        }

                    }
                    else{
                        $occurances[$idUnite] = array();
                        $occurances[$idUnite][$remarque] = 1;
                    }
                    $results[$key]['remarque'] = $remarque;

                }
                else
                {
                    $results[$key]['remarque'] = null; // mise à nul de la remarque sans importance.
                }

            }

            /*
             * Recupération du type patrouille si déjà existante
             */
            $type = null;
            $type = $em->getRepository('AppBundle:Type')->findOneBy(array('nom'=>'Patrouille'));

            if($type == null)
            {
                /*
                 * Creation du type Branche
                 */
                $type = new Type();
                $type->setNom('Patrouille');

                $newId = $this->getByOld('link_fonction',49); //49 = index CP
                $fonction = $em->getRepository('AppBundle:Fonction')->find($newId);

                $type->setFonctionChef($fonction);
                $type->setAffichageEffectifs(true);
                $em->persist($type);
                $em->flush();
            }



            /*
             * Création des patrouilles en se basant sur les occurances.
             */
            foreach($occurances as $idUnite => $listeOccurances)
            {
                foreach($listeOccurances as $nomPatrouille => $nb)
                {
                    if($nb > 3) //si le nombre d'occurance est suffisant, on crée la patrouile
                    {
                        if($this->isAlreadySet('link_groupe_unite',$idUnite))
                        {

                            $patrouille = new Groupe();
                            $patrouille->setNom($nomPatrouille);
                            $patrouille->setType($type);
                            $patrouille->setActive(true);

                            $newId = $this->getByOld('link_groupe_unite',$idUnite); //49 = index CP
                            $groupeParent = $em->getRepository('AppBundle:Groupe')->find($newId);

                            $existingPatrouille = false;
                            foreach($groupeParent->getEnfants() as $child)
                            {
                                echo $child->getNom(),' == ',$patrouille->getNom(),PHP_EOL;
                                if($child->getNom() == ucwords($nomPatrouille))
                                {
                                    $existingPatrouille = true;
                                }

                            }

                            if(!$existingPatrouille)
                            {
                                echo 'SAUVE:',$patrouille->getNom(),PHP_EOL;
                                $patrouille->setParent($groupeParent);
                                $em->persist($patrouille);
                                $em->flush();
                            }

                        }
                    }
                }
            }


            echo PHP_EOL,'Chargement des patrouilles => ok',PHP_EOL;
        }

    }


    private function formLatin1($str)
    {
        $str = strtolower($str);
        $str = iconv('LATIN1', 'ASCII//TRANSLIT', $str);
        $str = iconv('UTF-8', 'ASCII//IGNORE', $str);

        $tofind = '\'';
        $replac = '_';
        $str = strtr($str,$tofind,$replac);
        $tofind = '`';
        $replac = '_';
        $str = strtr($str,$tofind,$replac);

        return $str;
    }

    /*
     * =====> GESTION DES TABLES DE LIEN =======>
     */

    /**
     * @param $table
     * @param $old
     * @return mixed
     */
    private function getByOld($table,$old)
    {
        $sql = 'SELECT id_new FROM '.$table.' WHERE id_old = ?';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id_new', 'id_new');
        $query = $this->fichierBsEm->createNativeQuery($sql,$rsm)->setParameter(1,$old);
        $results = $query->getResult();
        return $results[0]['id_new'];

    }

    /**
     * @param $table
     * @param $old
     * @return bool
     */
    private function isAlreadySet($table,$old)
    {
        $sql = 'SELECT id_old FROM '.$table.' WHERE id_old = ?';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id_old', 'id_old');
        $query = $this->fichierBsEm->createNativeQuery($sql,$rsm)->setParameter(1,$old);
        $results = $query->getResult();
        if($results == null)
            return false;
        else
            return true;

    }

    /**
     * @param $table
     * @param $new
     * @param $old
     */
    private function setLink($table,$new,$old)
    {
        $sql = 'INSERT INTO '.$table.' (id_new ,id_old ) VALUES ('.$new.','.$old.')';
        $stmt = $this->connectionFichierBs->prepare($sql);
        $stmt->execute();
    }

    /**
     * @param $table
     *
     */
    private function createLinkTable($table)
    {
        $sql = 'CREATE TABLE '.$table.'( id_new int(11), id_old int(11) )';
        $stmt = $this->connectionFichierBs->prepare($sql);
        $stmt->execute();
    }

    /**
     * @param $table
     */
    private function deleteLinkTable($table)
    {
        $sql = 'DROP TABLE '.$table;
        $stmt = $this->connectionFichierBs->prepare($sql);
        $stmt->execute();
    }




}