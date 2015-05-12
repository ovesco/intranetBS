<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Attribution;
use AppBundle\Entity\ContactInformation;
use AppBundle\Entity\Membre;
use AppBundle\Entity\ObtentionDistinction;
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
            $editorMember = new Membre('John', 'Doe'); // TODO : we may find someone else than this guy for default

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
            if($entity instanceof ContactInformation) {
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

                    //$this->data_logger->member($editorMember, $modifiedMember, $detailedProperty, $values[0], $values[1]);
                    $this->historyEntry = new MemberHistory($editorMember, $modifiedMember, $detailedProperty, $values[0], $values[1]);
                }
            }


            if($entity instanceof ObtentionDistinction) {
                $obtentionDistinction = $entity;

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

        }
    }

    /**
     * Add the history entry to the DB
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
}

?>
