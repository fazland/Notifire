<?php declare(strict_types=1);

namespace Fazland\Notifire\Tests\Handler\Sms;

use Fazland\Notifire\Handler\Sms\SkebbyHandler;
use Fazland\Notifire\Notification\Sms;
use Fazland\Notifire\Result\Result;
use Fazland\SkebbyRestClient\Client\Client as SkebbyRestClient;
use Fazland\SkebbyRestClient\DataStructure\Response;
use Fazland\SkebbyRestClient\DataStructure\Sms as SkebbySms;
use Prophecy\Prophecy\ObjectProphecy;

class SkebbyHandlerTest extends AbstractSmsHandlerTest
{
    const RESPONSE_SUCCESS =
'<?xml version="1.0" encoding="UTF-8"?>
<SkebbyApi_Public_Send_SmsEasy_Advanced generator="zend" version="1.0"><test_send_sms_classic_report><remaining_sms>5</remaining_sms><id>1477056680</id><status>success</status></test_send_sms_classic_report></SkebbyApi_Public_Send_SmsEasy_Advanced>';

    const RESPONSE_FAIL =
'<?xml version="1.0" encoding="UTF-8"?>
<SkebbyApi_Public_Send_SmsEasy_Advanced generator="zend" version="1.0"><test_send_sms_classic><response><code>11</code><message>Unknown charset, use ISO-8859-1 or UTF-8</message></response><status>failed</status></test_send_sms_classic></SkebbyApi_Public_Send_SmsEasy_Advanced>';

    /**
     * @var SkebbyRestClient|ObjectProphecy
     */
    private $skebby;

    /**
     * {@inheritdoc}
     */
    public function getHandler()
    {
        $this->skebby = $this->prophesize(SkebbyRestClient::class);

        return new SkebbyHandler($this->skebby->reveal());
    }

    /**
     * @dataProvider right
     *
     * @param Sms $sms
     */
    public function testNotifyShouldCallSkebbySendAndReceiveSuccessfulResponse(Sms $sms)
    {
        $skebbySms = SkebbySms::create()
            ->setRecipients($sms->getTo())
            ->setText($sms->getContent())
        ;

        $response = new Response(self::RESPONSE_SUCCESS);

        $this->skebby->send($skebbySms)->shouldBeCalled();
        $this->skebby->send($skebbySms)->willReturn([$response]);

        $this->handler->notify($sms);

        /** @var Result $result */
        foreach ($sms->getResultSet()->all() as $result) {
            $this->assertTrue(Result::OK === $result->getResult());
        }
    }

    /**
     * @dataProvider right
     *
     * @expectedException \Fazland\Notifire\Exception\NotificationFailedException
     *
     * @param Sms $sms
     */
    public function testNotifyShouldCallSkebbySendReceiveFailingResponseAndThrowNotificationFailedException(Sms $sms)
    {
        $skebbySms = SkebbySms::create()
            ->setRecipients($sms->getTo())
            ->setText($sms->getContent())
        ;

        $response = new Response(self::RESPONSE_FAIL);

        $this->skebby->send($skebbySms)->shouldBeCalled();
        $this->skebby->send($skebbySms)->willReturn([$response]);

        $this->handler->notify($sms);

        /** @var Result $result */
        foreach ($sms->getResultSet()->all() as $result) {
            $this->assertTrue(Result::FAIL === $result->getResult());
        }
    }
}
