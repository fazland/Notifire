<?php

namespace Fazland\Notifire\Notification;

/**
 * Base class for notifications
 * Can be simply extended to create a new notification class
 * or the {@see NotificationTrait} can be used instead
 */
abstract class AbstractNotification implements NotificationInterface
{
    use NotificationTrait;
}
