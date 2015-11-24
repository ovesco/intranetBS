<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 24.11.15
 * Time: 10:25
 */

namespace AppBundle\EventSubscriber;

use Doctrine\Common\Persistence\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use AppBundle\Entity\Membre;
use AppBundle\Entity\Mail;
use Doctrine\Common\EventSubscriber;


class DynamicRelationSubscriber implements EventSubscriber {

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::loadClassMetadata,
        );
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {


        // the $metadata is the whole mapping info for this class
        /** @var ClassMetadata $metadata */
        $metadata = $eventArgs->getClassMetadata();

        $namingStrategy = $eventArgs
            ->getEntityManager()
            ->getConfiguration()
            ->getNamingStrategy()
        ;

        switch($metadata->getName())
        {
            case Membre::class:


                $metadata->mapOneToMany(array(
                    'targetEntity'  => Mail::CLASS,
                    'mappedBy' => 'mailableTrait',
                    'fieldName'     => 'mails',
                    'cascade'       => array('persist'),
                    /*
                    'joinTable'     => array(
                        'name'        => 'app_membre_mails',
                        'joinColumns' => array(
                            array(
                                'name'                  => $namingStrategy->joinKeyColumnName($metadata->getName()),
                                'referencedColumnName'  => $namingStrategy->referenceColumnName(),
                                'onDelete'  => 'CASCADE',
                                'onUpdate'  => 'CASCADE',
                            ),
                        ),
                        'inverseJoinColumns'    => array(
                            array(
                                'name'                  => 'mail_id',
                                'referencedColumnName'  => $namingStrategy->referenceColumnName(),
                                'onDelete'  => 'CASCADE',
                                'onUpdate'  => 'CASCADE',
                            ),
                        )
                    )
                    */
                ));


                break;
            Default:
                return;
        }



/*
        * $metadata->mapManyToMany(array(
                    'targetEntity'  => Address::CLASS,
                    'fieldName'     => 'addresses',
                    'cascade'       => array('persist'),
                    'joinTable'     => array(
                        'name'        => strtolower($namingStrategy->classToTableName($metadata->getName())) . '_addresses',
                        'joinColumns' => array(
                            array(
                                'name'                  => $namingStrategy->joinKeyColumnName($metadata->getName()),
                                'referencedColumnName'  => $namingStrategy->referenceColumnName(),
                                'onDelete'  => 'CASCADE',
                                'onUpdate'  => 'CASCADE',
                            ),
                        ),
                        'inverseJoinColumns'    => array(
                            array(
                                'name'                  => 'address_id',
                                'referencedColumnName'  => $namingStrategy->referenceColumnName(),
                                'onDelete'  => 'CASCADE',
                                'onUpdate'  => 'CASCADE',
                            ),
                        )
                    )
                ));

        */


    }
}