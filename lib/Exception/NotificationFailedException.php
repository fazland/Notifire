<?php

namespace Fazland\Notifire\Exception;

/**
 * This Exception is raised when:
 * - an error occurs while sending a notification
 * - no notifications were sent
 *
 * @author Alessandro Chitolina <alessandro.chitolina@fazland.com>
 */
class NotificationFailedException extends \Exception
{
    /**
     * @var array
     */
    private $details;

    public function __construct($message = '', $details = [], $code = 0, \Exception $previous = null)
    {
        $this->details = $details;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getDetails()
    {
        return $this->details;
    }
}
