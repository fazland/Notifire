<?php

namespace Fazland\Notifire\Exception;

/**
 * @author Stefano Rainieri <stefano.rainieri@fazland.com>
 *
 * This exception is raised when {@see Notifire::addNotification} is called and
 * $notificationClass is not an implementation of {@see NotificationInterface}.
 */
class UnsupportedClassException extends \Exception
{

}
