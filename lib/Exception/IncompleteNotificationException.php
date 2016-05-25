<?php

namespace Fazland\Notifire\Exception;

class IncompleteNotificationException extends \Exception
{
    /**
     * @var array
     */
    private $details;

    public function __construct($message = "", $details = [], $code = 0, \Exception $previous = null)
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
