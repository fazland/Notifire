<?php

namespace Fazland\Notifire\Exception;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 *
 * This exception is raised when a {@see NotificationInterface} is added
 * more than once with {@see Notifire::addNotification}.
 */
class NotificationAlreadyRegisteredException extends \Exception
{

}
