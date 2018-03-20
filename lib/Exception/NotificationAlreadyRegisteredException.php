<?php declare(strict_types=1);

namespace Fazland\Notifire\Exception;

/**
 * This exception is raised when a {@see NotificationInterface} is added
 * more than once with {@see Notifire::addNotification}.
 */
class NotificationAlreadyRegisteredException extends \InvalidArgumentException implements ExceptionInterface
{
}
