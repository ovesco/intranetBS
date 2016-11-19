<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 19.11.16
 * Time: 17:05
 */

namespace AppBundle\Utils\SessionTools;


class Notification {

    const ERROR = 'error';
    const INFO = 'info';
    const WARNING = 'warning';
    const SUCCESS = 'success';

    /** @var string */
    private $message;


    /** @var string */
    private $type;

    public function __construct($message,$type = self::INFO)
    {
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

}