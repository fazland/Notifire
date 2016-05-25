<?php

namespace Fazland\Notifire\Exception;

/**
 * This exception is raised when {@see Notifire::addNotification} is called and
 * $notificationClass is not an implementation of {@see NotificationInterface}.
 *
 * @author Stefano Rainieri <stefano.rainieri@fazland.com>
 */
class UnsupportedClassException extends \Exception
{

}
