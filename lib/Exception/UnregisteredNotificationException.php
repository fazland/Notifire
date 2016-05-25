<?php

namespace Fazland\Notifire\Exception;

/**
 * This exception is raised when {@see Notifire::factory(} is called
 * and there are no {@see NotificationInterface} registered under that
 * $notificationName.
 * 
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class UnregisteredNotificationException extends \Exception
{

}
