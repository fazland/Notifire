<?php

namespace Fazland\Notifire\Exception;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 *
 * This exception is raised when {@see Notifire::factory(} is called
 * and there are no {@see NotificationInterface} registered under that
 * $notificationName.
 */
class UnregisteredNotificationException extends \Exception
{

}
