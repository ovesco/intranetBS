<?php

namespace AppBundle\Utils\Log;

use AppBundle\Entity\Membre;
use Monolog\Logger;
use Symfony\Component\Security\Core\SecurityContext;

class DataLogger
{
    private $logger;

    private $securityContext;

    /**
     *
     *
     * @param Logger $logger
     */
    public function __construct(Logger $logger, SecurityContext $securityContext) {
        $this->logger = $logger;
        $this->securityContext = $securityContext;
    }

    public function member(Membre $membre, $attribute, $oldValue, $newValue, array $context = array()) {

        $log_message = $this->securityContext->getToken()->getUser()->getMembre() . ' a modifié ' . $membre . '|' . $attribute . ':' . $oldValue . ' > ' . $newValue;

        $this->logger->info($log_message, $context);
    }

}


?>