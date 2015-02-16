<?php

namespace AppBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;

class LogOnChangeListener {

    private $entityHelper;


    public function onFlush(OnFlushEventArgs $eventArgs)
    {

//        $em = $eventArgs->getEntityManager();
//        $uow = $em->getUnitOfWork();
//
//        $entity = $em->find('AppBundle\Entity\Membre', 1);
//        $changeset = $uow->getEntityChangeSet($entity);
//
//        foreach ($changeset as $change) {
//
//            //TODO: log the change
////            $app_logger = $this->get('data_logger');
////            $app_logger->member($this, "numéro AVS", $this->numeroAvs, $numeroAvs);
//
//
//        }
    }

    public function setEntityHelper($entityHelper)
    {
        $this->entityHelper = $entityHelper;

        return $this;
    }
}

?>