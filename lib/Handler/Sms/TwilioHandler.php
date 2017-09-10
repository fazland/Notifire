<?php

namespace Fazland\Notifire\Handler\Sms;

use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Notification\NotificationInterface;
use Fazland\Notifire\Notification\Sms;
use Fazland\Notifire\Result\Result;

/**
 * Twilio handler
 *
 * @author Daniele Rapisarda <daniele.rapisarda@fazland.com>
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
     * @param \Services_Twilio $twilio
     * @param string $name
     */
    public function __construct(\Services_Twilio $twilio, $name = 'default')
    {
        $this->twilio = $twilio;

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

            try {
                $response = $this->twilio->account->messages->create($params);
                $result->setResponse($response);
            } catch (\Exception $e) {
                $result->setResult(Result::FAIL)
                    ->setResponse($e);

                $failedSms[] = [
                    'to' => $to,
                    'error_message' => $e->getMessage(),
                ];
            }

            $notification->addResult($result);
        }

        if (count($tos) === count($failedSms)) {
            throw new NotificationFailedException('All the sms failed to be send', ['failed_sms' => $failedSms]);
        }
    }

    /**
     * Set the 'from' default. Used if no from is configured in the Sms object
     *
     * @param string $defaultFrom
     */
    public function setDefaultFrom($defaultFrom)
    {
        $this->defaultFrom = $defaultFrom;
    }

    /**
     * Get the parameters for the messages->create call
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
