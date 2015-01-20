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
use Interne\FinancesBundle\Entity\Creance;
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


        if($action == 'load'){

            ini_set('memory_limit', '-1'); //avoid memory limit exception!!!

            $errorReport = array(); //on affiche le rapport d'erreur à la fin

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
            $fonctions = array();
            foreach($results as $result)
            {
                $fonction = new Fonction(utf8_encode($result['nom']),utf8_encode($result['abreviation']));
                $em->persist($fonction);

                $fonctions[$result['id']] = $fonction;
            }
            $em->flush();

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
            $groupeRacine = array();
            foreach($results as $result)
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

                //on sauve le groupe racine pour être utilisé plus tard
                $groupeRacine[$result['id']] = $groupe;


                $em->persist($groupe);
            }
            $em->flush();

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
             * Creation du type Branche
             */
            $fonction = new Fonction('Chef de branche','CB');
            $type = new Type();
            $type->setNom('Branche');
            $type->setFonctionChef($fonction);
            $type->setAffichageEffectifs(true);
            $em->persist($fonction);
            $em->persist($type);

            /*
             * Enregistrement des branche dans la bs
             */
            $groupeBranche = array();
            foreach($results as $result)
            {
                $groupe = new Groupe();
                $groupe->setNom(utf8_encode($result['nom']));
                $groupe->setActive(true);
                $groupe->setParent($groupeRacine[1]);//groupe racine BS
                $groupe->setType($type);
                $em->persist($groupe);

                $groupeBranche[$result['id']] = $groupe;
            }
            $em->flush();

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

            /*
             * Enregistrement des unité dans leur branche
             */
            $groupeUnite = array();
            foreach($results as $result)
            {
                $groupe = new Groupe();
                $groupe->setNom(utf8_encode($result['nom']));
                $groupe->setActive(true);
                $groupe->setParent($groupeBranche[$result['branche']]);
                $groupe->setType($type);
                $em->persist($groupe);

                $groupeUnite[$result['id']] = $groupe;
            }
            $em->flush();

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
            $distinctions = array();
            foreach($results as $result)
            {
                $distinction = new Distinction();
                $distinction->setNom(utf8_encode($result['nom']));
                $distinction->setRemarques(utf8_encode($result['remarque']));
                $em->persist($distinction);

                $distinctions[$result['id']] = $distinction;
            }
            $em->flush();

            echo 'Chargement des distinctions => ok',PHP_EOL;



            /*
             * Chargement des familles
             */
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
            $familles = array();
            foreach($results as $result)
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

                $familles[$result['id']] = $famille;
                echo 'familles id: '.$result['id'].'=> ok',PHP_EOL;
            }
            $em->flush();

            echo 'Chargement des familles => ok',PHP_EOL;




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
            $membres = array();
            foreach($results as $result)
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
                        $em->persist($pere);
                    }
                    if($mere != null)
                    {
                        $famille->setMere($mere);
                        $em->persist($mere);
                    }


                }
                else
                {
                    if(isset($familles[$result['famille']]))
                    {
                        $famille = $familles[$result['famille']]; //on récupère la famille
                    }
                    else
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
                        $em->persist($pere);
                    }
                    if(($famille->getMere() == null)&&($mere != null))
                    {
                        $famille->setMere($mere);
                        $em->persist($mere);
                    }

                }


                //on crée le lien
                $famille->addMembre($membre);

                $em->persist($famille);


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

                $em->persist($membre);
                $em->persist($adresse);



                $em->flush();

                $membres[$result['id']] = $membre->getId();

                echo 'membre id: '.$result['id'].' (new id:'.$membre->getId().') => done',PHP_EOL;

            }

            echo 'Chargement des membre => ok',PHP_EOL;


            /*
             * Chargement du lien distinction/membre
             */
            $sql = 'SELECT membres_distinctions.id_membres_distinction, membres_distinctions.id_distinction, membres_distinctions.id_membre, membres_distinctions.date_membres_distinction FROM membres_distinctions';
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('id_membres_distinction', 'id');
            $rsm->addScalarResult('id_distinction', 'id_distinction');
            $rsm->addScalarResult('id_membre', 'id_membre');
            $rsm->addScalarResult('date_membres_distinction', 'date');
            $results = $fichierEm->createNativeQuery($sql,$rsm)->getResult();

            /*
             * Enregistrement des distinctions/membres
             */
            foreach($results as $result)
            {
                $link = new ObtentionDistinction();
                $link->setObtention(new \DateTime($result['date']));
                $link->setDistinction($distinctions[$result['id_distinction']]);

                $membre = $em->getRepository('AppBundle:Membre')->find($membres[$result['id_membre']]);


                $link->setMembre($membre);
                $em->persist($link);

                echo 'obtentionDistinction id: '.$result['id'].' => ok',PHP_EOL;
            }
            $em->flush();

            echo 'Chargement des distinctions <=> membres => ok',PHP_EOL;


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
                $remarque = strtolower(utf8_encode($result['remarque']));
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

            $type = new Type();
            $type->setNom('Patrouille');
            $type->setFonctionChef($fonctions[49]); //49 = index CP
            $type->setAffichageEffectifs(true);
            $em->persist($type);

            /*
             * Création des patrouilles en se basant sur les occurances.
             */
            $groupePatrouilles = array();
            foreach($occurances as $idUnite => $listeOccurances)
            {
                foreach($listeOccurances as $nomPatrouille => $nb)
                {
                    if($nb > 3) //si le nombre d'occurance est suffisant, on crée la patrouile
                    {
                        if(isset($groupeUnite[$idUnite]))
                        {
                            $patrouille = new Groupe();
                            $patrouille->setNom($nomPatrouille);
                            $patrouille->setType($type);
                            $patrouille->setActive(true);

                            $groupeParent = $groupeUnite[$idUnite];
                            $patrouille->setParent($groupeParent);

                            //on sauve les patrouilles dans un tableau pour être réutilisé
                            if(isset($groupePatrouilles[$idUnite]))
                            {
                                //ajout de la patrouille dans l'unité
                                $groupePatrouilles[$idUnite][$nomPatrouille] = $patrouille;
                            }
                            else
                            {
                                //première patrouille de l'unité
                                $groupePatrouilles[$idUnite] = array();
                                $groupePatrouilles[$idUnite][$nomPatrouille] = $patrouille;
                            }

                            $em->persist($patrouille);
                        }
                    }
                }
            }
            $em->flush();

            echo 'Chargement des patrouilles => ok',PHP_EOL;

            /*
             * Chargement des attributions/membres
             */

            foreach($results as $result)
            {


                /*
                 * On vérifie d'abord que l'attribution est consistante.
                 */
                $error = false;
                if(!isset($fonctions[$result['id_attribution']]))
                    $error = true;
                if(!isset($membres[$result['id_membre']]))
                    $error = true;
                if(!isset($groupeUnite[$idUnite]))
                    $error = true;

                if(!$error)
                {
                    $attribution = new Attribution();

                    $attribution->setDateDebut(new \DateTime($result['debut']));
                    if($result['fin'] != '0000-00-00')
                    {
                        $attribution->setDateFin(new \DateTime($result['fin']));
                    }
                    $attribution->setFonction($fonctions[$result['id_attribution']]);

                    $membre = $em->getRepository('AppBundle:Membre')->find($membres[$result['id_membre']]);
                    $attribution->setMembre($membre);

                    //TODO: le champ remarque n'est pas implémenté dans Attribution.php, on a donc une perte d'info

                    /*
                     * On met l'attribution dans le groupe mais on regarde si
                     * une patrouille correspond à la remarque.
                     */
                    $idUnite = $result['id_unite'];
                    $remarque = $result['remarque'];
                    $saved = false;
                    if(isset($groupePatrouilles[$idUnite]))
                    {
                        foreach($groupePatrouilles[$idUnite] as $nomPatrouille => $patrouille)
                        {
                            similar_text($remarque,$nomPatrouille,$percent);
                            if(($percent > 50)&&(!$saved))
                            {
                                //alors c'est la bonne patrouille et on l'enregistre.
                                $attribution->setGroupe($patrouille);
                                $saved = true;
                            }
                        }
                    }
                    if(!$saved) //aucune patrouille trouvée
                    {
                        $attribution->setGroupe($groupeUnite[$idUnite]);
                    }

                    $em->persist($attribution);

                    echo 'Attribution id: '.$result['id'].' => ok',PHP_EOL;
                }
                else
                {
                    array_push($errorReport,'erreur pour l\'attribution id: '.$result['id']);
                }



            }
            $em->flush();

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


            foreach($results as $result)
            {
                if(($result['id_famille'] != 0) or ($result['id_famille'] != 0))
                {
                    $creance = new Creance();
                    $creance->setMontantEmis($result['montantEmis']);
                    $creance->setMontantRecu($result['montantRecu']);
                    $creance->setDateCreation(new \DateTime($result['creation']));
                    $creance->setTitre(utf8_encode($result['nom']));
                    $creance->setRemarque(utf8_encode($result['remarque']));

                    /*
                     * On crée le lien entre les famille/membres et la facture
                     */
                    $facture = null;
                    if($result['id_famille'] != 0)
                    {
                        $facture = new FactureToFamille();
                        $facture->setFamille($familles[$result['id_famille']]);
                    }
                    elseif($result['id_membre'] != 0)
                    {
                        $membre = $em->getRepository('AppBundle:Membre')->find($membres[$result['id_membre']]);
                        $facture = new FactureToMembre();
                        $facture->setMembre($membre);

                    }
                    if($facture != null)
                    {
                        $facture->setDateCreation(new \DateTime($result['creation']));


                        /*
                         * Ajout des rappels
                         */
                        if($result['date_rappel_1'] != '0000-00-00')
                        {
                            $rappel1 = new Rappel();
                            $rappel1->setDateCreation(new \DateTime($result['date_rappel_1']));
                            $facture->addRappel($rappel1);
                            $em->persist($rappel1);
                        }
                        if($result['date_rappel_2'] != '0000-00-00')
                        {
                            $rappel2 = new Rappel();
                            $rappel2->setDateCreation(new \DateTime($result['date_rappel_2']));
                            $facture->addRappel($rappel2);
                            $em->persist($rappel2);
                        }

                        /*
                         * traitement en fonction du statut.
                         */
                        switch($result['statut'])
                        {
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

                        echo 'facture id: '.$result['id'].' (new id:'.$facture->getId().') => done',PHP_EOL;
                    }


                }


            }
            $em->flush();


            /*
             * Affichage du rapport d'erreur
             */
            foreach($errorReport as $error)
                echo $error,PHP_EOL;

        }

        elseif($action == 'test'){







        }

    }




}