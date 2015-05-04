<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Attribution;
use AppBundle\Entity\ContactInformation;
use AppBundle\Entity\Membre;
use AppBundle\Entity\ObtentionDistinction;
use AppBundle\Utils\Log\DataLogger;
use Faker\Provider\cs_CZ\DateTime;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;

class LogOnChangeListener {

    private $data_logger;
    private $token_storage;

    public function __construct(TokenStorageInterface $token_storage, DataLogger $data_logger)
    {
        $this->token_storage = $token_storage;
        $this->data_logger = $data_logger;
    }

    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $uow = $eventArgs->getEntityManager()->getUnitOfWork();
        $uow->computeChangeSets();

        if($this->token_storage->getToken() != NULL)
            $editorMember = $this->token_storage->getToken()->getUser()->getMembre();
        else
            $editorMember = new Membre('John', 'Doe');

        /* Get all updated entities */
        foreach ($uow->getScheduledEntityUpdates() as $entity) {

            $changeSet = $uow->getEntityChangeSet($entity);

            /* Log members */
            if($entity instanceof Membre) {
                $modifiedMember = $entity;

                foreach ($changeSet as $property => $values) {
                    $this->data_logger->member($editorMember, $modifiedMember, $property, $values[0], $values[1]);
                }
            }

            /* Log ContactInformation changes */
            if($entity instanceof ContactInformation) {
                $contactInformation = $entity;

                $modifiedMember = $eventArgs->getEntityManager()->getRepository('AppBundle:Membre')->find($contactInformation->getContact()->getId());

                if($modifiedMember != NULL) {
                    foreach ($changeSet as $property => $values) {
                        $detailedProperty = $property . ' (' . $contactInformation . ')';
                        $this->data_logger->member($editorMember, $modifiedMember, $detailedProperty, $values[0], $values[1]);
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

                    $this->data_logger->member($editorMember, $modifiedMember, $detailedProperty, $values[0], $values[1]);
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

                    $this->data_logger->member($editorMember, $modifiedMember, $detailedProperty, $values[0], $values[1]);
                }

            }

        }
    }

    public function setEntityHelper($entityHelper)
    {
        $this->entityHelper = $entityHelper;

        return $this;
    }
}

?>
