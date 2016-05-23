<?php

namespace Fazland\Notifire\Notification;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
interface NotificationInterface
{
    /**
     * Returns the name of the notification.
     * 
     * @return string
     */
    public function getName();

    /**
     * Implementors MUST dispatch an {@see NotifyEvent}.
     */
    public function send();
}
