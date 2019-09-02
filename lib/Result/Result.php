<?php declare(strict_types=1);

namespace Fazland\Notifire\Result;

/**
 * Represents a notification sending result
 * Holds error details if any.
 */
class Result
{
    public const OK = 'ok';
    public const FAIL = 'fail';

    /**
     * The Handler class name.
     *
     * @var string
     */
    private $handlerName;

    /**
     * The subservice that handled this request
     * (mailer, twilio service, etc).
     *
     * @var string
     */
    private $service;

    /**
     * Result code.
     *
     * @var string
     */
    private $result;

    /**
     * The result target.
     *
     * @var string
     */
    private $target;

    /**
     * External service response, if any.
     *
     * @var mixed
     */
    private $response;

    /**
     * Constructor.
     *
     * @param string $handlerName
     * @param string $service
     * @param string $result
     */
    public function __construct(string $handlerName, string $service, string $result = self::OK)
    {
        $this->handlerName = $handlerName;
        $this->service = $service;
        $this->result = $result;
    }

    /**
     * @return string
     */
    public function getHandlerName(): string
    {
        return $this->handlerName;
    }

    /**
     * @return string
     */
    public function getService(): string
    {
        return $this->service;
    }

    /**
     * @return string
     */
    public function getResult(): string
    {
        return $this->result;
    }

    /**
     * @param string $result
     *
     * @return $this
     */
    public function setResult(string $result): self
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Is notification ok?
     *
     * @return bool
     */
    public function isOk(): bool
    {
        return self::OK === $this->result;
    }

    /**
     * Notification has encountered an error.
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return ! $this->isOk();
    }

    /**
     * Get the result target (could be an email address, a phone number for sms, etc).
     *
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * Set the result target.
     *
     * @param string $target
     *
     * @return $this
     */
    public function setTarget(string $target): self
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get error details.
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set the error details.
     *
     * @param mixed $response
     *
     * @return $this
     */
    public function setResponse($response): self
    {
        $this->response = $response;

        return $this;
    }
}
