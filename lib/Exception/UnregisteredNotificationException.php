<?php declare(strict_types=1);

namespace Fazland\Notifire\Exception;

/**
 * This exception is raised when {@see Notifire::factory(} is called
 * and there are no {@see NotificationInterface} registered under that
 * $notificationName.
 */
class UnregisteredNotificationException extends \Exception
{
}
