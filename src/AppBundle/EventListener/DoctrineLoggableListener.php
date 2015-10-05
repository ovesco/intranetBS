<?php

namespace AppBundle\EventListener;
use AppBundle\Entity\CustomLogEntry;
use Gedmo\Loggable\LoggableListener;
use Gedmo\Loggable\Mapping\Event\LoggableAdapter;
use Gedmo\Tool\Wrapper\AbstractWrapper;


/**
 * Override du loggableListener par d�faut afin de permettre la validation de modifications,
 * et de garder une trace de la personne qui a modifi�.
 */
class DoctrineLoggableListener extends LoggableListener {


    /**
     * Set username for identification
     *
     * @param mixed $username
     *
     * @throws \Gedmo\Exception\InvalidArgumentException Invalid username
     */
    public function setUsername($username)
    {

        if (is_string($username)) {
            $this->username = $username;
        } elseif (is_object($username) && method_exists($username, 'getUsername')) {
            $this->username = (string) $username->getUsername();
        } else {
            throw new \Gedmo\Exception\InvalidArgumentException("Username must be a string, or object should have method: getUsername");
        }
    }







    /**
     * On surcharge �galement la m�thode qui g�n�re la custom logEntry afin de
     * g�n�rer la notre OKLM
     * @param string $action
     * @param object $object
     * @param LoggableAdapter $ea
     * @return \Gedmo\Loggable\Entity\LogEntry|null|void
     */
    protected function createLogEntry($action, $object, LoggableAdapter $ea)
    {
        $om = $ea->getObjectManager();
        $wrapped = AbstractWrapper::wrap($object, $om);
        $meta = $wrapped->getMetadata();

        // Filter embedded documents
        if (isset($meta->isEmbeddedDocument) && $meta->isEmbeddedDocument) {
            return;
        }

        if ($config         = $this->getConfiguration($om, $meta->name)) {
            $logEntryClass  = 'AppBundle\\Entity\\CustomLogEntry';   // Notre classe de log !
            $logEntryMeta   = $om->getClassMetadata($logEntryClass);
            $logEntry       = $logEntryMeta->newInstance();         /** @var \AppBundle\Entity\CustomLogEntry $logEntry */

            $logEntry->setAction($action);
            $logEntry->setUsername($this->username);
            $logEntry->setObjectClass($meta->name);
            //$logEntry->setUser(null);
            $logEntry->setStatus(CustomLogEntry::$WAITING);
            $logEntry->setLoggedAt();

            // check for the availability of the primary key
            $uow = $om->getUnitOfWork();
            if ($action === self::ACTION_CREATE && $ea->isPostInsertGenerator($meta)) {
                $this->pendingLogEntryInserts[spl_object_hash($object)] = $logEntry;
            } else {
                $logEntry->setObjectId($wrapped->getIdentifier());
            }
            $newValues = array();
            if ($action !== self::ACTION_REMOVE && isset($config['versioned'])) {
                $newValues = $this->getObjectChangeSetData($ea, $object, $logEntry);
                $logEntry->setData($newValues);
            }

            if($action === self::ACTION_UPDATE && 0 === count($newValues)) {
                return null;
            }

            $version = 1;
            if ($action !== self::ACTION_CREATE) {
                $version = $ea->getNewVersion($logEntryMeta, $object);
                if (empty($version)) {
                    // was versioned later
                    $version = 1;
                }
            }
            $logEntry->setVersion($version);

            $this->prePersistLogEntry($logEntry, $object);

            $om->persist($logEntry);
            $uow->computeChangeSet($logEntryMeta, $logEntry);

            return $logEntry;
        }

        return null;
    }
}
