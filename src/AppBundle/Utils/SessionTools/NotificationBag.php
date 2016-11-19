<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 19.11.16
 * Time: 16:59
 */

namespace AppBundle\Utils\SessionTools;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class NotificationBag {

    const notification_bag_key = 'notification_bag_key';

    /** @var ArrayCollection */
    private $notifications;


    /** @var  SessionInterface */
    private $session;

    public function __construct(SessionInterface $session){

        $this->session = $session;

        //get the sessioned array of notification or a new one if not set.
        $this->notifications = $this->session->get(self::notification_bag_key,new ArrayCollection());

        $this->session->set(self::notification_bag_key,$this->notifications);
    }

    public function addNotification(Notification $notification)
    {
        $this->notifications->add($notification);
        $this->session->set(self::notification_bag_key,$this->notifications);
    }

    public function clearNotifications()
    {
        $this->notifications->clear();
        $this->session->set(self::notification_bag_key,$this->notifications);
    }

    /**
     * @return ArrayCollection
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * @param ArrayCollection $notifications
     */
    public function setNotifications($notifications)
    {
        $this->notifications = $notifications;
    }

}