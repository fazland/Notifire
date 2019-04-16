<?php declare(strict_types=1);

namespace Fazland\Notifire\Handler\Sms;

use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Notification\NotificationInterface;
use Fazland\Notifire\Result\Result;
use Fazland\SkebbyRestClient\Client\Client as SkebbyRestClient;
use Fazland\SkebbyRestClient\DataStructure\Sms as SkebbySms;

class SkebbyHandler extends AbstractSmsHandler
{
    /**
     * @var SkebbyRestClient
     */
    private $skebby;

    /**
     * @param SkebbyRestClient $skebby
     * @param string           $name
     */
    public function __construct(SkebbyRestClient $skebby, string $name = 'default')
    {
        parent::__construct($name);

        $this->skebby = $skebby;
    }

    /**
     * {@inheritdoc}
     */
    public function notify(NotificationInterface $notification)
    {
        $failedSms = [];
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
                $response = $this->skebby->send($skebbySms)[0];
                $this->logger->debug('Response from Skebby '.(string) $response);

                $result->setResponse($response);
                if (! $response->isSuccessful()) {
                    $result->setResult(Result::FAIL);

                    $failedSms[] = [
                        'to' => $to,
                        'response_status' => $response->getStatus(),
                        'error_code' => $response->getCode(),
                        'error_message' => $response->getErrorMessage(),
                    ];
                }
            } catch (\Exception $e) {
                $result
                    ->setResult(Result::FAIL)
                    ->setResponse($e)
                ;

                $failedSms[] = [
                    'to' => $to,
                    'error_message' => $e->getMessage(),
                ];

                $this->logger->debug('Exception from Skebby: '.$e->getMessage(), ['exception' => $e]);
            }

            $notification->addResult($result);
        }

        if (\count($tos) === \count($failedSms)) {
            throw new NotificationFailedException('All the sms failed to be send', ['failed_sms' => $failedSms]);
        }
    }
}
