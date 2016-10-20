<?php

namespace Fazland\Notifire\Handler\Sms;

use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Notification\NotificationInterface;
use Fazland\SkebbyRestClient\Client\Client as SkebbyRestClient;
use Fazland\SkebbyRestClient\DataStructure\Sms as SkebbySms;
use Fazland\Notifire\Result\Result;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class SkebbyHandler extends AbstractSmsHandler
{
    /**
     * @var SkebbyRestClient
     */
    private $skebby;

    /**
     * @param SkebbyRestClient $skebby
     * @param string $name
     */
    public function __construct(SkebbyRestClient $skebby, $name = 'default')
    {
        parent::__construct($name);

        $this->skebby = $skebby;
    }

    /**
     * {@inheritdoc}
     */
    public function notify(NotificationInterface $notification)
    {
        $tos = $notification->getTo();
        if (empty($tos)) {
            throw new NotificationFailedException('No recipients specified');
        }

        foreach ($tos as $to) {
            $skebbySms = SkebbySms::create()
                ->addRecipient($to)
                ->setText($notification->getContent())
            ;

            $result = new Result('skebby', $this->name, Result::OK);

            try {
                $response = $this->skebby->send($skebbySms);
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
    }
}
