<?php declare(strict_types=1);

namespace Fazland\Notifire\Exception;

/**
 * This exception is raised when {@see Notifire::addNotification} is called and
 * $notificationClass is not an implementation of {@see NotificationInterface}.
 */
class UnsupportedClassException extends \Exception
{
}
