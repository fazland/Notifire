<?php

namespace Fazland\Notifire\Exception;

/**
 * This exception is raised when a {@see NotificationInterface} is added
 * more than once with {@see Notifire::addNotification}.
 *
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class NotificationAlreadyRegisteredException extends \Exception
{
}
