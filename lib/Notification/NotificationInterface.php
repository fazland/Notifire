<?php

namespace Fazland\Notifire\Notification;

/**
 * Core interface of Notifire notification system. This Interface
 * MUST be implemented in order to register a notification in Notifire.
 *
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
interface NotificationInterface
{
    /**
     * Implementors MUST dispatch an {@see NotifyEvent}.
     */
    public function send();
}
