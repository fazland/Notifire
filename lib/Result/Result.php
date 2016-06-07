<?php

namespace Fazland\Notifire\Result;

/**
 * Represents a notification sending result
 * Holds error details if any
 */
class Result
{
    const OK = 'ok';
    const FAIL = 'fail';

    /**
     * The Handler class name
     *
     * @var string
     */
    private $handlerName;

    /**
     * The subservice that handled this request
     * (mailer, twilio service, etc)
     *
     * @var string
     */
    private $service;

    /**
     * Result code
     *
     * @var string
     */
    private $result;

    /**
     * The result target
     *
     * @var string
     */
    private $target;

    /**
     * External service response, if any
     *
     * @var mixed
     */
    private $response;

    /**
     * Constructor
     *
     * @param string $handlerName
     * @param string $service
     * @param string $result
     */
    public function __construct($handlerName, $service, $result = self::OK)
    {
        $this->handlerName = $handlerName;
        $this->service = $service;
        $this->result = $result;
    }

    /**
     * @return string
     */
    public function getHandlerName()
    {
        return $this->handlerName;
    }

    /**
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param string $result
     *
     * @return $this
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Is notification ok?
     *
     * @return bool
     */
    public function isOk()
    {
        return $this->result === self::OK;
    }

    /**
     * Notification has encountered an error
     *
     * @return bool
     */
    public function hasErrors()
    {
        return ! $this->isOk();
    }

    /**
     * Get the result target (could be an email address, a phone number for sms, etc)
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set the result target
     *
     * @param string $target
     *
     * @return $this
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Set the error details
     *
     * @param mixed $response
     *
     * @return $this
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get error details
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }
}
