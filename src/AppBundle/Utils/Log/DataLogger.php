<?php

namespace AppBundle\Utils\Log;

use AppBundle\Entity\Membre;
use Monolog\Logger;

class DataLogger
{
    private $logger;

    private $securityContext;

    /**
     *
     *
     * @param Logger $logger
     */
    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    public function member(Membre $editor, Membre $modifiedMember, $attribute, $oldValue, $newValue, array $context = array()) {

        if($newValue == $oldValue)
            return;

        elseif($oldValue == '')
            $log_message = sprintf(
                "%s a été modifié par %s | %s '%s' a été ajouté",
                $modifiedMember,
                $editor,
                ucfirst($attribute),
                $newValue);

        elseif($newValue == '')
            $log_message = sprintf(
                "%s a été modifié par %s | %s '%s' a été éffacé",
                $modifiedMember,
                $editor,
                ucfirst($attribute),
                $oldValue);
        else
            $log_message = sprintf(
                "%s a été modifié par %s | %s a été de changé de '%s' à '%s'",
                $modifiedMember,
                $editor,
                ucfirst($attribute),
                $oldValue,
                $newValue);

        $this->logger->info($log_message, $context);
    }

}

?>