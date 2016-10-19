<?php

namespace Fazland\Notifire\Tests\RestClient\Skebby;

use Fazland\Notifire\RestClient\Skebby\Constant\Charsets;
use Fazland\Notifire\RestClient\Skebby\Constant\SendMethods;
use Fazland\Notifire\RestClient\Skebby\Response;
use Fazland\Notifire\RestClient\Skebby\RestClient as SkebbyRestClient;
use Fazland\Notifire\RestClient\Skebby\Sms;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 * @runTestsInSeparateProcesses
 */
class RestClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SkebbyRestClient
     */
    private $skebbyRestClient;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->skebbyRestClient = new SkebbyRestClient([
            'username' => 'test',
            'password' => 'test',
            'user_reference' => 'test',
            'sender_number' => '+393333333333',
            'charset' => Charsets::UTF8,
            'method' => SendMethods::CLASSIC,
        ]);
    }

    /**
     * @return array
     */
    public function noRecipients()
    {
        return [
            [Sms::create()->setText('some text')],
        ];
    }

    /**
     * @return array
     */
    public function recipients()
    {
        return [
            [
                Sms::create()
                    ->setRecipients([
                        '+393473322444',
                        '+393910000000',
                    ])
                    ->setText('Some text'),
            ],
        ];
    }

    /**
     * @return array
     */
    public function recipientsAndRecipientsVariables()
    {
        return [
            [
                Sms::create()
                    ->setRecipients([
                        '+393473322444',
                        '+393910000000',
                    ])
                    ->setRecipientVariables('+393473322444', [
                        'FirstName' => 'This is a first name',
                        'LastName' => 'This is a last name',
                        'Infos' => 'These are infos',
                    ])
                    ->setRecipientVariables('+393910000000', [
                        'FirstName' => 'This is another first name',
                        'LastName' => 'This is another last name',
                        'Infos' => 'These are other infos',
                    ])
                    ->setText('Some text'),
            ],
        ];
    }

    /**
     * @dataProvider noRecipients
     * @expectedException \Fazland\Notifire\Exception\NoRecipientsSpecifiedException
     *
     * @param Sms $sms
     */
    public function testSendShouldThrowNoRecipientSpecifiedExceptionOnEmptyRecipient(Sms $sms)
    {
        $this->skebbyRestClient->send($sms);
    }

    /**
     * @dataProvider recipients
     * @expectedException \Fazland\Notifire\Exception\EmptyResponseException
     *
     * @param Sms $sms
     */
    public function testSendShouldThrowEmptyResponseExceptionOnEmptyResponse(Sms $sms)
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

        $this->skebbyRestClient->send($sms);
    }

    /**
     * @dataProvider recipients
     * @expectedException \Fazland\Notifire\Exception\UnknownErrorResponseException
     *
     * @param Sms $sms
     */
    public function testSendShouldThrowUnknownErrorResponseExceptionOnResponseWithoutStatus(Sms $sms)
    {
        eval(<<<'EOT'
?><?php

namespace Fazland\Notifire\RestClient\Skebby 
{
    function curl_init() { }
    
    function curl_setopt($curl, $option, $value) { }
    
    function curl_exec()
    {
        return "this=is&a=response&without=status";
    }
    
    function curl_close() { }
}
EOT
        );

        $this->skebbyRestClient->send($sms);
    }

    /**
     * @dataProvider recipients
     *
     * @param Sms $sms
     */
    public function testSendShouldReturnResponses(Sms $sms)
    {
        eval(<<<'EOT'
?><?php

namespace Fazland\Notifire\RestClient\Skebby 
{
    function curl_init() { }
    
    function curl_setopt($curl, $option, $value) { }
    
    function curl_exec()
    {
        return "status=success&message=";
    }
    
    function curl_close() { }
}
EOT
        );

        $responses = $this->skebbyRestClient->send($sms);

        foreach ($responses as $response) {
            $this->assertInstanceOf(Response::class, $response);
        }
    }
}
