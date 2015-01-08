<?php

namespace AppBundle\Utils\Email;

use \Swift_Attachment;

/**
 * Service: 'email'
 * Class Email
 * @package AppBundle\Utils\Email
 */
class Email extends \Swift_Message
{
    public function __construct()
    {
        parent::__construct();
        $this->newInstance();
    }

    /**
     * Fonction pour simplifier la syntaxe d'ajout de pièces jointes
     * @param $filePath
     * @param null $fileName
     */
    public function attachFile($filePath,$fileName = null)
    {
        $attachement = \Swift_Attachment::fromPath($filePath);
        if($fileName != null)
            $attachement->setFilename($fileName);
        $this->attach($attachement);
    }
}