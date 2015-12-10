<?php

namespace AppBundle\Command;

use AppBundle\Entity\Adresse;
use AppBundle\Entity\Attribution;
use AppBundle\Entity\Categorie;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Distinction;
use AppBundle\Entity\Email;
use AppBundle\Entity\Fonction;
use AppBundle\Entity\Pere;
use AppBundle\Entity\Mere;
use AppBundle\Entity\Groupe;
use AppBundle\Entity\Famille;
use AppBundle\Entity\Membre;
use AppBundle\Entity\Model;
use AppBundle\Entity\Personne;
use AppBundle\Entity\ObtentionDistinction;
use AppBundle\Entity\Telephone;
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
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Connection;
use AppBundle\Utils\Data\Sanitizer;
use Interne\SecurityBundle\Entity\User;
use Exception;
use Symfony\Component\Console\Helper\ProgressBar;


/**
 * todo mise à jours de ce ficier importante
 *
 * Class FichierBsCommand
 * @package AppBundle\Command
 *
 *
 * Utilisation:
 * 1) désactivé elasticsearch (commentaire dans le fichier de config)
 * 2) reset (si les tables de liens sont déjà existante
 * 3) prepare
 * 4) load (ctrl+c pour s'arreter au milieu et s'affranchir du probleme de mémoire...ensuite: "load" à nouveau
 *
 */
class FichierBsCommand extends ContainerAwareCommand
{
    /** @var OutputInterface $output */
    private $output;
    /** @var EntityManager $em */
    private $em;
    /** @var EntityManager $fichierEm */
    private $fichierBsEm;
    /** @var Connection connectionFichierBs */
    private $connectionFichierBs;

    private $linkTables;

    protected function configure()
    {
        $this
            ->setName('app:fichier')
            ->setDescription('Remplir la base de donnée')
            ->addArgument('action', InputArgument::REQUIRED, 'Quel action souhaitez-vous faire?')
        ;

        ini_set('memory_limit', '-1'); //avoid memory limit exception!!!

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        /*
         * Chargement des arguements
         */
        $action = $input->getArgument('action');

        /*
         * Chargement des deux base de données
         */
        $this->em  = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->fichierBsEm = $this->getContainer()->get('doctrine')->getManager('fichier');
        $this->connectionFichierBs = $this->fichierBsEm->getConnection();

        $this->linkTables = array('link_fonction',
            'link_distinction','link_famille',
            'link_membre','link_groupe_racine',
            'link_groupe_branche','link_groupe_unite',
            'link_membre_distinctions','link_facture',
            'link_attribution_membre'
        );


        switch($action){
            case 'reset':
                /*
                 * A utiliser lorsqu'on veut recommancer. (supprime les tables de lien)
                 */
                $this->deleteProcessTable();
                foreach($this->linkTables as $table)
                    $this->deleteLinkTable($table);
                break;

            case 'prepare':
                /*
                 * A utiliser avant l'action "load" (cree les tables de lien)
                 */
                $this->createProcessTable();
                foreach($this->linkTables as $table)
                    $this->createLinkTable($table);
                break;

            case 'load':

                $processFlow = array('Attributions','Groupe_racine','Branches','Unites','Distinctions','Familles','Membres','Distinctions_Membres','Attributions_Membres','Factures');

                foreach($processFlow as $process)
                {
                    if(!$this->isProcessDone($process))
                    {
                        $this->output('Start loading: '.$process,'info');
                        switch($process){
                            case 'Attributions':
                                $this->loadAttributions();
                                break;
                            case 'Groupe_racine':
                                $this->loadRootGroups();
                                break;
                            case 'Branches':
                                $this->loadBranche();
                                break;
                            case 'Unites':
                                $this->loadUnits();
                                break;
                            case 'Distinctions':
                                $this->loadDistinctions();
                                break;
                            case 'Familles':
                                $this->loadFamilles();
                                break;
                            case 'Membres':
                                $this->loadMembres();
                                break;
                            case 'Distinctions_Membres':
                                $this->loadDistinctionsMembres();
                                break;
                            case 'Attributions_Membres':
                                $this->loadAttributionsMembres();
                                break;
                            case 'Factures':
                                $this->loadFacture();
                                break;
                        }

                        $this->setProcessDone($process);
                        $this->MemoryClean();
                        $this->output('Finish loading: '.$process.'=> <info>ok</info>','info');
                    }
                    else
                    {
                        $this->output('Already loaded: '.$process,'info');
                    }
                }

                break;
        }



    }







    /*
     * ======== FONCTIONS SPECIFIQUES ========
     */

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

    private function output($string,$mode = null){

        if (OutputInterface::VERBOSITY_VERBOSE <= $this->output->getVerbosity()) {

            switch($mode){
                case null;
                    break;
                case 'error':
                    $string = '<error>Error:</error> '.$string;
                    break;
                case 'info':
                    $string = '<info>Info:</info> '.$string;
                    break;
            }
            $this->output->writeln(PHP_EOL.$string);
        }
    }

    private function MemoryClean(){

        $this->em->clear();
        $this->fichierBsEm->clear();
        gc_collect_cycles();

    }
    /*
     * ===== GESTION DES TABLES DE LIEN =======
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

    private function setLink($table,$new,$old)
    {
        $sql = 'INSERT INTO '.$table.' (id_new ,id_old ) VALUES ('.$new.','.$old.')';
        $stmt = $this->connectionFichierBs->prepare($sql);
        $stmt->execute();
    }

    private function createLinkTable($table)
    {
        $sql = 'CREATE TABLE '.$table.'( id_new int(11), id_old int(11) )';
        $stmt = $this->connectionFichierBs->prepare($sql);
        $stmt->execute();
    }

    private function deleteLinkTable($table)
    {
        $sql = 'DROP TABLE '.$table;
        $stmt = $this->connectionFichierBs->prepare($sql);
        $stmt->execute();
    }

    private function createProcessTable()
    {
        $sql = 'CREATE TABLE loading_process_done ( process_done varchar(255) )';
        $stmt = $this->connectionFichierBs->prepare($sql);
        $stmt->execute();
    }

    private function setProcessDone($process){
        $sql = 'INSERT INTO loading_process_done (process_done) VALUES ("'.$process.'")';
        $stmt = $this->connectionFichierBs->prepare($sql);
        $stmt->execute();
    }

    private function isProcessDone($process){
        $sql = 'SELECT process_done FROM loading_process_done WHERE process_done = ?';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('process_done','process_done');
        $query = $this->fichierBsEm->createNativeQuery($sql,$rsm)->setParameter(1,$process);
        $results = $query->getResult();
        if($results == null)
            return false;
        else
            return true;
    }

    private function deleteProcessTable()
    {
        $sql = 'DROP TABLE loading_process_done';
        $stmt = $this->connectionFichierBs->prepare($sql);
        $stmt->execute();
    }


    /*
     * ======== FONCTIONs DE TRANSFERT =======
     */

    /**
     * CALL in 1st
     */
    private function loadAttributions(){
        /*
         * Chargement des attributions (fonction dans AppBundle)
         */
        $sql = 'SELECT attributions.id_attribution, attributions.nom_attribution, attributions.abrev_attribution FROM attributions';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id_attribution', 'id');
        $rsm->addScalarResult('nom_attribution', 'nom');
        $rsm->addScalarResult('abrev_attribution', 'abreviation');
        $results = $this->fichierBsEm->createNativeQuery($sql,$rsm)->getResult();

        /*
         * Enregistrement des fonctions
         */
        foreach($results as $result)
        {
            if(!$this->isAlreadySet('link_fonction',$result['id']))
            {
                //création de la fonction
                $fonction = new Fonction(utf8_encode($result['nom']),utf8_encode($result['abreviation']));
                $this->em->persist($fonction);
                $this->em->flush();

                //on sauve le lien
                $this->setLink('link_fonction',$fonction->getId(),$result['id']);


            }
        }
    }

    /**
     * CALL in 2nd
     */
    private function loadRootGroups(){
        /*
         * On crée la hierachie des groupes pour commencer. En sortant les fichiers.
         */
        $sql = 'SELECT fichiers.id_fichier, fichiers.nom_fichier FROM fichiers';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id_fichier', 'id');
        $rsm->addScalarResult('nom_fichier', 'nom');
        $results = $this->fichierBsEm->createNativeQuery($sql,$rsm)->getResult();


        /*
         * On charge les fichiers comme groupe principaux
         */
        foreach($results as $result)
        {
            if(!$this->isAlreadySet('link_groupe_racine',$result['id']))
            {
                //$fonction = new Fonction('A mofifier','A modifier');
                $model = new Model();
                $model->setNom('A modifier');
                //$model->setFonctionChef($fonction);
                $model->setAffichageEffectifs(true);
                //$em->persist($fonction);
                $this->em->persist($model);

                $groupe = new Groupe();
                $groupe->setNom($result['nom']);
                $groupe->setActive(true);
                $groupe->setParent(null);//groupe racine
                $groupe->setModel($model);

                $this->em->persist($groupe);
                $this->em->flush();

                //on sauve le lien
                $this->setLink('link_groupe_racine',$groupe->getId(),$result['id']);
            }

        }
    }

    /**
     * CALL in 3rd
     */
    private function loadBranche(){
        /*
         * On charge les branches
         */
        $sql = 'SELECT branches.id_branche, branches.nom_branche FROM branches';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id_branche', 'id');
        $rsm->addScalarResult('nom_branche', 'nom');
        $results = $this->fichierBsEm->createNativeQuery($sql,$rsm)->getResult();

        /*
         * Recupération de la branche si déjà existante
         */
        $model = null;
        $model = $this->em->getRepository('AppBundle:Model')->findOneBy(array('nom'=>'Branche'));

        if($model == null)
        {
            /*
             * Creation du type Branche
             */
            $fonction = new Fonction('Chef de branche','CB');
            $model = new Model();
            $categorie = new Categorie('Branche');
            $model->setNom('Branche');
            $model->setFonctionChef($fonction);
            $model->setAffichageEffectifs(true);
            $model->addCategorie($categorie);
            $this->em->persist($fonction);
            $this->em->persist($model);
            $this->em->persist($categorie);
        }


        /*
         * Enregistrement des branche dans la bs
         */
        foreach($results as $result)
        {
            if(!$this->isAlreadySet('link_groupe_branche',$result['id']))
            {
                $newId = $this->getByOld('link_groupe_racine',1);//groupe racine BS
                $groupeRacine = $this->em->getRepository('AppBundle:Groupe')->find($newId);

                $groupe = new Groupe();
                $groupe->setNom(utf8_encode($result['nom']));
                $groupe->setActive(true);
                $groupe->setParent($groupeRacine);//groupe racine BS
                $groupe->setModel($model);
                $this->em->persist($groupe);
                $this->em->flush();

                //on sauve le lien
                $this->setLink('link_groupe_branche',$groupe->getId(),$result['id']);
            }

        }
    }

    /**
     * CALL in 4th
     */
    private function loadUnits(){


        /*
         * Chargement des unités
         */
        $sql = 'SELECT unites.id_unite, unites.nom_unite, unites.id_branche FROM unites';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id_unite', 'id');
        $rsm->addScalarResult('nom_unite', 'nom');
        $rsm->addScalarResult('id_branche', 'branche');
        $results = $this->fichierBsEm->createNativeQuery($sql,$rsm)->getResult();


        //Récupération de l'unité si existante
        $model = null;
        $model = $this->em->getRepository('AppBundle:Model')->findOneBy(array('nom'=>'Unité'));

        if($model == null)
        {
            /*
             * Creation du type Unité
             */
            $fonction = new Fonction('Chef d\'unité','CU');
            $model = new Model();
            $categorie = new Categorie('Unité');
            $model->setNom('Unité');
            $model->setFonctionChef($fonction);
            $model->setAffichageEffectifs(true);
            $model->addCategorie($categorie);
            $this->em->persist($categorie);
            $this->em->persist($fonction);
            $this->em->persist($model);
        }

        /*
         * Enregistrement des unité dans leur branche
         */
        foreach($results as $result)
        {
            if(!$this->isAlreadySet('link_groupe_unite',$result['id']))
            {
                $newId = $this->getByOld('link_groupe_branche',$result['branche']);
                $branche = $this->em->getRepository('AppBundle:Groupe')->find($newId);

                $groupe = new Groupe();
                $groupe->setNom(utf8_encode($result['nom']));
                $groupe->setActive(true);
                $groupe->setParent($branche);
                $groupe->setModel($model);
                $this->em->persist($groupe);
                $this->em->flush();

                //on sauve le lien
                $this->setLink('link_groupe_unite',$groupe->getId(),$result['id']);
            }

        }
    }

    /**
     * CALL in 5th
     */
    private function loadDistinctions(){
        /*
         * Chargement des distinctions
         */
        $sql = 'SELECT distinctions.id_distinction, distinctions.nom_distinction, distinctions.remarques_distinction FROM distinctions';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id_distinction', 'id');
        $rsm->addScalarResult('nom_distinction', 'nom');
        $rsm->addScalarResult('remarques_distinction', 'remarque');
        $results = $this->fichierBsEm->createNativeQuery($sql,$rsm)->getResult();

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
                $this->em->persist($distinction);
                $this->em->flush();

                //on sauve le lien
                $this->setLink('link_distinction',$distinction->getId(),$result['id']);

            }
        }
    }

    /**
     * CALL in 6th
     */
    private function loadFamilles(){

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
        $results = $this->fichierBsEm->createNativeQuery($sql,$rsm)->getResult();


        /*
         * Enregistrement des familles
         */
        $progress = new ProgressBar($this->output, count($results));
        $progress->start();
        foreach($results as $result)
        {
            try{
                if(!$this->isAlreadySet('link_famille',$result['id']))
                {
                    $adresse = new Adresse();
                    $adresse->setRue(utf8_encode($result['rue']));
                    $adresse->setNpa(utf8_encode($result['npa']));
                    $adresse->setLocalite(utf8_encode($result['ville']));
                    $adresse->setExpediable(true);

                    $contact = new Contact();
                    $contact->setAdresse($adresse);

                    $famille = new Famille();
                    $famille->setNom(utf8_encode($result['nom']));
                    $famille->setValidity(2);
                    $famille->setContact($contact);

                    $this->em->persist($adresse);
                    $this->em->persist($contact);
                    $this->em->persist($famille);
                    $this->em->flush();

                    //on sauve le lien
                    $this->setLink('link_famille',$famille->getId(),$result['id']);
                    echo '.';
                }


            }catch (\Exception $e){
                echo 'Erreur: '.$e->getMessage(),' => Famille id_old:'.$result['id'],PHP_EOL;
            }
            $progress->advance();

        }
        $progress->finish();
    }

    /**
     * CALL in 7th
     */
    private function loadMembres(){

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


        $results = $this->fichierBsEm->createNativeQuery($sql,$rsm)->getResult();


        /*
         * Enregistrement des membres
         */

        /*
         * création d'un tableau des membre existant pour
         * reconstituer les familles
         */
        $membre_array = [];
        $membres = $this->em->getRepository('AppBundle:Membre')->findAll();
        /** @var Membre $existing_membre */
        foreach($membres as $existing_membre)
        {
            $famille = $existing_membre->getFamille();

            $pere = $famille->getPere();
            $pere_prenom = null;
            if($pere != null)
            {
                $pere_prenom = $pere->getPrenom();
            }


            $mere = $famille->getMere();
            $mere_prenom = null;
            if($mere != null)
            {
                $mere_prenom = $mere->getPrenom();
            }


            /*
             * on stock les infos de la famille pour voir si
             * il y a des familles qui on les meme info par la suite.
             */
            $membre_array[] = array(
                'id_membre'=>$existing_membre->getId(),
                'nom_famille'=>$famille->getNom(),
                'prenom_pere'=>$pere_prenom,
                'prenom_mere'=>$mere_prenom,
            );
        }

        $progress = new ProgressBar($this->output, count($results));
        $progress->start();

        foreach($results as $result)
        {
            try
            {
                if(!$this->isAlreadySet('link_membre',$result['id']))
                {

                    $membre = new Membre();
                    $membre->setValidity(2);
                    $membre->setNaissance(new \DateTime($result['naissance']));

                    /*
                     * Création des géniteurs
                     */
                    $pere = null;
                    $mere = null;
                    if($result['prenom_pere'] != null)
                    {
                        $pere = new Pere();
                        $pere->setNom(utf8_encode($result['nom_pere']));
                        $pere->setPrenom(utf8_encode($result['prenom_pere']));
                        $pere->setSexe(Personne::HOMME);
                        $pere->setProfession(utf8_encode($result['profession_pere']));

                        $contact_pere = new Contact();
                        $pere->setContact($contact_pere);


                    }
                    if($result['prenom_mere'] != null)
                    {
                        $mere = new Mere();
                        $mere->setNom(utf8_encode($result['nom_mere']));
                        $mere->setPrenom(utf8_encode($result['prenom_mere']));
                        $mere->setSexe(Personne::FEMME);
                        $mere->setProfession(utf8_encode($result['profession_mere']));

                    }

                    /*
                     * Lien avec la famille
                     */
                    $famille = null;
                    if($result['famille'] == 0) //0 = sans famille dans l'ancien fichier
                    {

                        /*
                         * Attention! c'est pas parce que 0 dans la table de lien que il n'y
                         * a pas de frere et soeur. dans l'ancien fichier, on supprime la famille
                         * après désincription...Du coup il faut reconstituer maintenant.
                         *
                         *
                         */

                        /*
                         * On cherche si la famille existe dans le tableau de $membres_array
                         */

                        $match_found = false;
                        foreach($membre_array as $existing_membre)
                        {
                            if(!$match_found)
                            {
                                if($existing_membre['nom_famille']==utf8_encode($result['nom']))
                                {

                                    similar_text($existing_membre['prenom_pere'],utf8_encode($result['prenom_pere']),$percent_pere);
                                    similar_text($existing_membre['prenom_mere'],utf8_encode($result['prenom_mere']),$percent_mere);

                                    if(($percent_pere > 50) || ($percent_mere > 50)){
                                        /*
                                         * On admet que c'est la meme famille si ces conditions sont remplie.
                                         */

                                        /** @var Membre $membre_in_db */
                                        $membre_in_db = $this->em->getRepository('AppBundle:Membre')->find($existing_membre['id_membre']);
                                        $famille = $membre_in_db->getFamille();
                                        $match_found = true;

                                    }

                                }
                            }

                        }

                        /*
                         * Si aucune famille trouvée, on crée une nouvelle famille.
                         */
                        if(!$match_found) {

                            $famille = new Famille(); //donc nouvelle famille
                            $famille->setNom(utf8_encode($result['nom']));
                            $famille->setValidity(0);

                            $contactFamille = new Contact();
                            $famille->setContact($contactFamille);

                            // on sauve les géniteurs si ils ont été crée
                            if($pere != null)
                            {
                                $famille->setPere($pere);
                            }
                            if($mere != null)
                            {
                                $famille->setMere($mere);
                            }
                        }


                    }
                    else
                    {
                        $newId = $this->getByOld('link_famille',$result['famille']);

                        $famille = $this->em->getRepository('AppBundle:Famille')->find($newId);

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


                    }

                    /*
                     * Si les géniteurs était inexistant.
                     */
                    if(($famille->getPere() == null)&&($pere != null))
                    {
                        $famille->setPere($pere);
                    }
                    if(($famille->getMere() == null)&&($mere != null))
                    {
                        $famille->setMere($mere);
                    }


                    //on crée le lien
                    $famille->addMembre($membre);




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

                    if($result['sexe'] == 'f')
                    {
                        $membre->setSexe(Personne::FEMME);
                    }
                    else{
                        $membre->setSexe(Personne::HOMME);
                    }




                    $adresse = new Adresse();
                    $adresse->setRue(utf8_encode($result['rue']));
                    $adresse->setNpa(utf8_encode($result['npa']));
                    $adresse->setLocalite(utf8_encode($result['ville']));
                    $adresse->setExpediable(true);

                    $contact = new Contact();
                    $contact->setAdresse($adresse);
                    if(($result['email'] != null) && ($result['email'] != ''))
                    {
                        $contact->addEmail(new Email(utf8_encode($result['email'])));
                    }



                    //on sauve le téléphone avec une priorité sur les numéros de natel
                    if(($result['tel'] != null) && ($result['tel'] != '') )
                        $contact->addTelephone(new Telephone(utf8_encode($result['tel'])));
                    if(($result['natel'] != null) && ($result['natel'] != '') )
                        $contact->addTelephone(new Telephone(utf8_encode($result['natel'])));



                    //on ajoute l'adresse au membre
                    $membre->setContact($contact);


                    //fin de création du membre. la famille est le haut de la pyramide des "cascade"
                    $this->em->persist($famille);



                    /*
                     * CREATE USER
                     *
                     * On ajoute un user de manière automatique
                     * au membre nouvellement créé
                     */
                    $user = new User();
                    $user->setUsername(Sanitizer::cleanNames($membre->getPrenom()) . "." . $membre->getNom());

                    $password = substr(md5($user->getUsername()),0,7);

                    $user->setPassword($password);
                    $user->setMembre($membre);
                    $user->setLastConnexion(new \Datetime());

                    try{
                        $this->em->persist($user);
                    }catch (Exception $e){

                        echo 'Exception reçue : ',  $e->getMessage(), "\n";

                        return null;
                    }



                    try{
                        $this->em->persist($contact);
                    }catch (Exception $e){

                        echo 'Exception reçue : ',  $e->getMessage(), "\n";

                        return null;
                    }

                    try{
                        $this->em->flush();
                    }catch (Exception $e){

                        echo 'Exception reçue : ',  $e->getMessage(), "\n";

                        return null;
                    }




                    $pere = $famille->getPere();
                    $pere_prenom = null;
                    if($pere != null)
                    {
                        $pere_prenom = $pere->getPrenom();
                    }


                    $mere = $famille->getMere();
                    $mere_prenom = null;
                    if($mere != null)
                    {
                        $mere_prenom = $mere->getPrenom();
                    }


                    /*
                     * on stock les infos de la famille pour voir si
                     * il y a des familles qui on les meme info par la suite.
                     */
                    $membre_array[] = array(
                        'id_membre'=>$membre->getId(),
                        'nom_famille'=>$famille->getNom(),
                        'prenom_pere'=>$pere_prenom,
                        'prenom_mere'=>$mere_prenom,
                    );



                    $this->setLink('link_membre',$membre->getId(),$result['id']);


                    //petite amélioration pour la mémoire.
                    $this->MemoryClean();


                }






            }
            catch(\Exception $e)
            {
                $this->output($e->getMessage().' => Membre id_old:'.$result['id'],'error');
            }
            $progress->advance();
        }

        $progress->finish();

    }

    /**
     * CALL in 8th
     */
    private function loadDistinctionsMembres(){



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
        $results = $this->fichierBsEm->createNativeQuery($sql,$rsm)->getResult();

        /*
         * Enregistrement des distinctions/membres
         */
        $progress = new ProgressBar($this->output, count($results));
        $progress->start();
        foreach($results as $result)
        {
            try{

                if(!$this->isAlreadySet('link_membre_distinctions',$result['id']))
                {
                    $newId = $this->getByOld('link_membre',$result['id_membre']);
                    $membre = $this->em->getRepository('AppBundle:Membre')->find($newId);

                    $newId = $this->getByOld('link_distinction',$result['id_distinction']);
                    $distinction = $this->em->getRepository('AppBundle:Distinction')->find($newId);

                    $link = new ObtentionDistinction();
                    $link->setDate(new \DateTime($result['date']));
                    $link->setDistinction($distinction);
                    $link->setMembre($membre);
                    $this->em->persist($link);
                    $this->em->flush();

                    $this->setLink('link_membre_distinctions',$link->getId(),$result['id']);



                }



            }
            catch(\Exception $e)
            {
                $this->output($e->getMessage(),' => obtentionDistinction id_old:'.$result['id'],'error');
            }
            $progress->advance();
        }
        $progress->finish();

    }

    /**
     * CALL in 9th
     */
    private function loadAttributionsMembres(){

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
        $results = $this->fichierBsEm->createNativeQuery($sql,$rsm)->getResult();


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
        $model = null;
        $model = $this->em->getRepository('AppBundle:Model')->findOneBy(array('nom'=>'Patrouille'));

        if($model == null)
        {
            /*
             * Creation du type Branche
             */
            $model = new Model();
            $categorie = new Categorie('Sous-unité');
            $model->setNom('Patrouille');

            $model->addCategorie($categorie);
            $newId = $this->getByOld('link_fonction',49); //49 = index CP
            $fonction = $this->em->getRepository('AppBundle:Fonction')->find($newId);

            $model->setFonctionChef($fonction);
            $model->setAffichageEffectifs(true);
            $this->em->persist($categorie);
            $this->em->persist($model);
            $this->em->flush();
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
                        $patrouille->setModel($model);
                        $patrouille->setActive(true);

                        $newId = $this->getByOld('link_groupe_unite',$idUnite);
                        $groupeParent = $this->em->getRepository('AppBundle:Groupe')->find($newId);

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
                            $this->em->persist($patrouille);
                            $this->em->flush();
                        }

                    }
                }
            }
        }

        $this->output('Chargement des patrouilles <=> membres','info');

        /*
         * Chargement des attributions/membres
         */
        $progress = new ProgressBar($this->output, count($results));
        $progress->start();

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
                        $membre = $this->em->getRepository('AppBundle:Membre')->find($newId);

                        $newId = $this->getByOld('link_fonction',$result['id_attribution']);
                        $fonction = $this->em->getRepository('AppBundle:Fonction')->find($newId);

                        $newId = $this->getByOld('link_groupe_unite',$result['id_unite']);
                        $unite = $this->em->getRepository('AppBundle:Groupe')->find($newId);





                        $attribution = new Attribution();

                        $attribution->setDateDebut(new \DateTime($result['debut']));
                        if($result['fin'] != '0000-00-00')
                        {
                            $attribution->setDateFin(new \DateTime($result['fin']));
                        }
                        $attribution->setFonction($fonction);
                        $attribution->setMembre($membre);


                        /*
                         * On met l'attribution dans le groupe mais on regarde si
                         * une patrouille correspond à la remarque.
                         */
                        $remarque = $this->formLatin1($result['remarque']);

                        $attribution->setRemarques($remarque);
                        $saved = false;



                        /** @var Groupe $patrouille */
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

                        $this->em->persist($attribution);
                        $this->em->flush();

                        //on sauve le lien
                        $this->setLink('link_attribution_membre',$attribution->getId(),$result['id']);

                        $this->MemoryClean();


                    }
                }


            }catch(\Exception $e)
            {
                $this->output($e->getMessage(),' => Attribution id_old:'.$result['id'],'error');
            }
            $progress->advance();
        }
        $progress->finish();
    }

    /**
     * CALL in 10th
     */
    private function loadFacture(){



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
        $results = $this->fichierBsEm->createNativeQuery($sql,$rsm)->getResult();




        $progress = new ProgressBar($this->output, count($results));
        $progress->start();

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
                            $famille = $this->em->getRepository('AppBundle:Famille')->find($newId);
                        }
                        $membre = null;
                        if($this->isAlreadySet('link_membre',$result['id_membre']))
                        {
                            $newId = $this->getByOld('link_membre', $result['id_membre']);
                            $membre = $this->em->getRepository('AppBundle:Membre')->find($newId);
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
                            $this->em->persist($rappel1);
                        }
                        if ($result['date_rappel_2'] != '0000-00-00') {
                            $rappel2 = new Rappel();
                            $rappel2->setDateCreation(new \DateTime($result['date_rappel_2']));
                            $facture->addRappel($rappel2);
                            $this->em->persist($rappel2);
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
                        $this->em->persist($creance);
                        $this->em->persist($facture);
                        $this->em->flush();

                        //on sauve le lien
                        $this->setLink('link_facture',$facture->getId(),$result['id']);


                    }


                    $this->MemoryClean();
                }
            }
            catch(\Exception $e)
            {
                $this->output($e->getMessage(),' => Facture id_old:'.$result['id'],'error');
            }
            $progress->advance();

        }
        $progress->finish();
    }


}