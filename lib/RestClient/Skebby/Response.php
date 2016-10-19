<?php

namespace Fazland\Notifire\RestClient\Skebby;

use Fazland\Notifire\Exception\EmptyResponseException;
use Fazland\Notifire\Exception\UnknownErrorResponseException;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class Response
{
    const GENERIC_ERROR = 10;
    const INVALID_CHARSET = 11;
    const MISSING_MANDATORY_PARAM = 12;
    const INVALID_PARAMETERS = 20;
    const INVALID_USERNAME_OR_PASSWORD = 21;
    const INVALID_SENDER = 22;
    const SENDER_LENGTH_TOO_LONG = 23;
    const TEXT_TOO_LONG = 24;
    const INVALID_RECIPIENT = 25;
    const MISSING_SENDER = 26;
    const TOO_MANY_RECIPIENTS = 27;
    const ACCOUNT_UNABLE_TO_USE_SMS_GATEWAY = 29;
    const INSUFFICIENT_CREDIT = 30;
    const INVALID_REQUEST = 31;
    const INVALID_DELIVERY_START_PARAM = 32;
    const INVALID_ENCODING_SCHEME = 33;
    const INVALID_VALIDITY_PERIOD = 34;
    const INVALID_USER_REFERENCE = 35;
    const MISSING_USER_REFERENCE = 36;
    const CHARACTERS_NOT_IN_CURRENT_CHARSET = 37;
    const TOO_MANY_ALISA_WITH_SAME_VAT = 38;
    const INVALID_VAT = 39;
    const ALPHA_NUMERIC_SENDER_ALLOWED_ONLY_FOR_BUSINESS_PLANS = 40;
    const ALPHA_NUMERIC_SENDER_ALREADY_REGISTERED = 41;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $errorMessage;

    /**
     * @var string
     */
    private $messageId;

    /**
     * @param array $rawResponse
     *
     * @throws EmptyResponseException
     * @throws UnknownErrorResponseException
     */
    public function __construct($rawResponse)
    {
        if (empty($rawResponse)) {
            throw new EmptyResponseException();
        }

        parse_str($rawResponse, $response);

        if (! isset($response['status'])) {
            throw new UnknownErrorResponseException("Missing response status value from Skebby");
        }

        $this->status = $response['status'];
        $this->code = isset($response['code']) ? $response['code'] : null;
        $this->errorMessage = isset($response['message']) ? $response['message'] : "Unknown error";
        $this->messageId = isset($response['id']) ? $response['id'] : null;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function isSuccessful()
    {
        return "success" === $this->status;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @return string
     */
    public function getMessageId()
    {
        return $this->messageId;
    }
}
