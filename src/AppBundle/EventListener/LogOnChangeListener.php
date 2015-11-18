<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Adresse;
use AppBundle\Entity\Attribution;
use AppBundle\Entity\Contact;
use AppBundle\Entity\ContactInformation;
use AppBundle\Entity\Distinction;
use AppBundle\Entity\Email;
use AppBundle\Entity\Famille;
use AppBundle\Entity\Membre;
use AppBundle\Entity\Mere;
use AppBundle\Entity\Modification;
use AppBundle\Entity\ObtentionDistinction;
use AppBundle\Entity\Pere;
use AppBundle\Entity\Telephone;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Interne\HistoryBundle\Entity\MemberHistory;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LogOnChangeListener {

    private $token_storage;

    /** @var MemberHistory $historyEntry */
    private $historyEntry;

    /** @var Session $session */
    private $session;

    public function __construct(TokenStorageInterface $token_storage, Session $session)
    {
        $this->token_storage = $token_storage;
        $this->session = $session;
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $uow = $eventArgs->getEntityManager()->getUnitOfWork();

        if($this->token_storage->getToken() != NULL)
            $editorMember = $this->token_storage->getToken()->getUser()->getMembre();
        else
        {
            $editorMember = new Membre(); // TODO : we may find someone else than this guy for default
            $editorMember->setPrenom('John');
        }


        /* Get all updated entities */
        foreach ($uow->getScheduledEntityUpdates() as $entity) {

            $changeSet = $uow->getEntityChangeSet($entity);

            /* Log members */
            if($entity instanceof Membre) {
                $modifiedMember = $entity;

                foreach ($changeSet as $property => $values) {

                    //$this->data_logger->member($editorMember, $modifiedMember, $property, $values[0], $values[1]);
                    $this->historyEntry = new MemberHistory($editorMember, $modifiedMember, $property, $values[0], $values[1]);
                }
            }

            /* Log ContactInformation changes */
            if($entity instanceof Contact) {
                $contactInformation = $entity;

                $modifiedMember = $eventArgs->getEntityManager()->getRepository('AppBundle:Membre')->find($contactInformation->getContact()->getId());

                if($modifiedMember != NULL) {
                    foreach ($changeSet as $property => $values) {
                        $detailedProperty = $property . ' (' . $contactInformation . ')';
                        //$this->data_logger->member($editorMember, $modifiedMember, $detailedProperty, $values[0], $values[1]);
                        $this->historyEntry = new MemberHistory($editorMember, $modifiedMember, $detailedProperty, $values[0], $values[1]);
                    }
                }
            }

            if($entity instanceof Attribution) {
                $attribution = $entity;

                $modifiedMember = $attribution->getMembre();

                foreach ($changeSet as $property => $values) {
                    $detailedProperty = $property . ' (' . $attribution . ')';

                    if($values[0] != NULL && $values[0] instanceof \DateTime)
                        $values[0] = $values[0]->format('d.m.Y'); // TODO: take date format from globals

                    if($values[1] != NULL && $values[1] instanceof \DateTime)
                        $values[1] = $values[1]->format('d.m.Y'); // TODO: take date format from globals

                    $this->historyEntry = new MemberHistory($editorMember, $modifiedMember, $detailedProperty, $values[0], $values[1]);
                }
            }


            if($entity instanceof ObtentionDistinction) {
                $obtentionDistinction = $entity;

                $modifiedMember = $obtentionDistinction->getMembre();

                foreach ($changeSet as $property => $values) {
                    $detailedProperty = $property . ' (' . $obtentionDistinction . ')';

                    if($values[0] != NULL && $values[0] instanceof \DateTime)
                        $values[0] = $values[0]->format('d.m.Y'); // TODO: take date format from globals

                    if($values[1] != NULL && $values[1] instanceof \DateTime)
                        $values[1] = $values[1]->format('d.m.Y'); // TODO: take date format from globals

                    //$this->data_logger->member($editorMember, $modifiedMember, $detailedProperty, $values[0], $values[1]);
                    $this->historyEntry = new MemberHistory($editorMember, $modifiedMember, $detailedProperty, $values[0], $values[1]);
                }
            }


            /*
             * Gestion des modifications et de la validation des changements effectués sur la base de donnée
             * Tous les changements sur la BDD engendrent la création d'un objet modification, qui servira de contrôle et permettra
             * de retourner à l'état précédent si nécessaire.
             *
             * Cet outil ne s'applique pas aux membres ayant les roles suffisants
             */
            $token = $this->token_storage->getToken();

            if(!$token->getUser()->hasRole('ROLE_SWAG')) { //TODO : mettre un role plus cool et virer le !

                $path               = "";
                $em                 = $eventArgs->getEntityManager();
                $entityFromContact  = function($source, $contact) use ($em) {

                    $potentialMembre   = $em->getRepository('AppBundle:Membre')->find($contact->getId());
                    $potentialFamille  = $em->getRepository('AppBundle:Famille')->find($contact->getId());
                    $potentialGeniteur = $em->getRepository('AppBundle:Geniteur')->find($contact->getId());

                    if($potentialMembre != null)
                        return "membre." . $potentialMembre->getId() . "." . $source;
                    elseif($potentialFamille != null)
                        return "famille." . $potentialFamille->getId() . "." . $source;

                    elseif($potentialGeniteur != null) {

                        if($potentialGeniteur instanceof Mere)
                            return "famille." . $potentialGeniteur->getFamille()->getId() . ".mere." . $source;
                        else if($potentialGeniteur instanceof Pere)
                            return "famille." . $potentialGeniteur->getFamille()->getId() . ".pere." . $source;
                        else
                            return null;

                    }
                    else
                        throw new \Exception("Goddamit Jack, j'ai pas trouvé l'entité yo");
                };

                /*
                 * ICI :
                 * On récupère l'entité "racine" de celle qui subit la modification, un membre ou une famille
                 * TODO : Faire un truc mieux si possible

                if($entity instanceof Membre)
                    $path           = "membre." . $entity->getId();
                elseif($entity instanceof Famille)
                    $path           = "famille." . $entity->getId();
                elseif($entity instanceof Pere)
                    $path           = "famille." . $entity->getFamille()->getId() . ".pere";
                elseif($entity instanceof Mere)
                    $path           = "famille." . $entity->getFamille()->getId() . ".mere";
                elseif($entity instanceof Attribution)
                    $path           = "membre." . $entity->getMembre()->getId() . ".attributions[" . $entity->getMembre()->getAttributions()->indexOf($entity) . "]";
                elseif($entity instanceof ObtentionDistinction)
                    $path           = "membre." . $entity->getMembre()->getId() . ".obtentionDistinctions[" . $entity->getMembre()->getDistinctions()->indexOf($entity) . "]";
                elseif($entity instanceof Contact)
                    $path           = $entityFromContact('contact', $entity);
                elseif($entity instanceof Telephone)
                    $path           = $entityFromContact('contact.telephones[' . $entity->getContact()->getTelephones()->indexOf($entity) . "]", $entity->getContact());
                elseif($entity instanceof Email)
                    $path           = $entityFromContact('contact.emails[' . $entity->getContact()->getEmails()->indexOf($entity) . "]", $entity->getContact());
                elseif($entity instanceof Adresse)
                    $path           = $entityFromContact('contact.adresse', $entity->getContact());

                // Génération d'un objet Modification dans le cas où c'est nécessaire
                if($path != null) {

                    foreach($changeSet as $property => $values) {

                        $modification = new Modification();

                        $modification->setOldValue($values[0]);
                        $modification->setNewValue($values[1]);
                        $modification->setAuteur($token->getUser()->getMembre());
                        $modification->setDate(new \DateTime());
                        $modification->setPath($path . '.' . $property);
                        $modification->setStatut(Modification::EN_ATTENTE);

                        $em->persist($modification);
                    }

                    $uow->computeChangeSets();
                }
                */

            }

        }
    }

    /**
     * Add the history entry to the DB
     *
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        if (!empty($this->historyEntry)) {

            $this->historyEntry->setSession($this->session->getId());

            $em = $args->getEntityManager();
            $em->persist($this->historyEntry);

            /* We must clean entry before flush, else we get infinite recursion */
            $this->historyEntry = NULL;

            $em->flush();
        }

    }

    public function setEntityHelper($entityHelper)
    {
        $this->entityHelper = $entityHelper;

        return $this;
    }

    /**
     * Génère un path lisible pour retracer l'origine de la modification
     * par exemple Membre[1]->famille->pere->adresse->rue
     * @param $linkedEntity
     * @param $entity
     * @param $field
     */
    private function generatePath($linkedEntity, $entity, $field) {


    }
}