<?php declare(strict_types=1);

namespace Fazland\Notifire\Handler\Sms;

use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Notification\NotificationInterface;
use Fazland\Notifire\Notification\Sms;
use Fazland\Notifire\Result\Result;
use Twilio\Exceptions\RestException;
use Twilio\Rest\Client;

/**
 * Twilio handler.
 */
class TwilioHandler extends AbstractSmsHandler
{
    /**
     * @var \Services_Twilio
     */
    private $twilio;

    /**
     * @var string
     */
    private $defaultFrom;

    /**
     * @var string
     */
    private $messagingServiceSid;

    /**
     * @param \Services_Twilio|Client $twilio
     * @param string           $name
     */
    public function __construct($twilio, string $name = 'default')
    {
        $this->twilio = $twilio;

        if (! $twilio instanceof \Services_Twilio && ! $twilio instanceof Client) {
            throw new \TypeError('Expected ' . \Services_Twilio::class . ' or ' . Client::class . '. Got ' . get_class($twilio));
        }

        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    public function notify(NotificationInterface $notification)
    {
        $failedSms = [];

        /** @var Sms $notification */
        $data = $this->getData($notification);
        $tos = $notification->getTo();

        foreach ($tos as $to) {
            $result = new Result('twilio', $this->name, Result::OK);
            $params = $data;
            $params['To'] = $to;

            if (null !== $this->messagingServiceSid) {
                $params['MessagingServiceSid'] = $this->messagingServiceSid;
            }

            try {
                if ($this->twilio instanceof Client) {
                    unset($params['To']);

                    $params = array_combine(array_map('lcfirst', array_keys($params)), $params);

                    $response = $this->twilio->messages->create($to, $params);
                } else {
                    $response = $this->twilio->account->messages->create($params);
                }

                $this->logger->debug('Response from Twilio ' . (string)$response);

                $result->setResponse($response);
            } catch (RestException $e) {
                $result->setResult(Result::FAIL)
                    ->setResponse($e);

                $failedSms[] = [
                    'to' => $to,
                    'response_status' => $e->getStatusCode(),
                    'error_message' => $e->getMessage(),
                ];

                $this->logger->debug(get_class($e).' from Twilio: '.$e->getMessage(), [
                    'exception' => $e,
                    'response_status' => $e->getStatusCode(),
                ]);
            } catch (\Services_Twilio_RestException $e) {
                $result->setResult(Result::FAIL)
                    ->setResponse($e);

                $failedSms[] = [
                    'to' => $to,
                    'response_status' => $e->getStatus(),
                    'error_info' => $e->getInfo(),
                    'error_message' => $e->getMessage(),
                ];

                $this->logger->debug(get_class($e).' from Twilio: '.$e->getMessage(), [
                    'exception' => $e,
                    'response_status' => $e->getStatus(),
                    'error_info' => $e->getInfo(),
                ]);
            } catch (\Throwable $e) {
                $result->setResult(Result::FAIL)
                    ->setResponse($e);

                $failedSms[] = [
                    'to' => $to,
                    'error_message' => $e->getMessage(),
                ];

                $this->logger->debug('Generic Exception from Twilio: '.$e->getMessage(), ['exception' => $e]);
            }

            $notification->addResult($result);
        }

        if (count($tos) === count($failedSms)) {
            throw new NotificationFailedException('All the sms failed to be send', ['failed_sms' => $failedSms]);
        }
    }

    public function setMessagingServiceSid(string $messagingServiceSid)
    {
        $this->messagingServiceSid = $messagingServiceSid;
    }

    /**
     * Set the 'from' default. Used if no from is configured in the Sms object.
     *
     * @param string $defaultFrom
     */
    public function setDefaultFrom(string $defaultFrom)
    {
        $this->defaultFrom = $defaultFrom;
    }

    /**
     * Get the parameters for the messages->create call.
     *
     * @param Sms $notification
     *
     * @return array
     */
    protected function getData(Sms $notification)
    {
        return [
            'From' => $notification->getFrom() ?: $this->defaultFrom,
            'Body' => $notification->getContent(),
        ];
    }
}
