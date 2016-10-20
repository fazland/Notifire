<?php

namespace Fazland\Notifire\Tests\Handler\Sms;

use Fazland\Notifire\Handler\Sms\SkebbyHandler;
use Fazland\Notifire\Notification\Sms;
use Fazland\SkebbyRestClient\Client\Client as SkebbyRestClient;
use Fazland\SkebbyRestClient\DataStructure\Sms as SkebbySms;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class SkebbyHandlerTest extends AbstractSmsHandlerTest
{
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
    public function testNotifyShouldCallSkebbySend(Sms $sms)
    {
        eval(<<<'EOT'
?><?php

namespace Fazland\Notifire\RestClient\Skebby 
{
    function curl_init() { }
    
    function curl_setopt($curl, $option, $value) { }
    
    function curl_exec()
    {
        return "";
    }
    
    function curl_close() { }
}
EOT
        );

        $skebbySms = SkebbySms::create()
            ->setRecipients($sms->getTo())
            ->setText($sms->getContent())
        ;

        $this->skebby->send($skebbySms)->shouldBeCalled();

        $this->handler->notify($sms);
    }
}
