<?php

namespace Fazland\Notifire\RestClient\Skebby;

use Fazland\Notifire\Exception\NoRecipientsSpecifiedException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class RestClient
{
    const REST_ENDPOINT_HTTP = 'http://gateway.skebby.it/api/send/smseasy/advanced/rest.php';
    const REST_ENDPOINT_HTTPS = 'https://gateway.skebby.it/api/send/smseasy/advanced/rest.php';

    const METHOD_CLASSIC = 'send_sms_classic';
    const METHOD_CLASSIC_PLUS = 'send_sms_classic_report';
    const METHOD_BASIC = 'send_sms_basic';
    const METHOD_TEST_CLASSIC = 'test_send_sms_classic';
    const METHOD_TEST_CLASSIC_PLUS = 'test_send_sms_classic_report';
    const METHOD_TEST_BASIC = 'test_send_sms_basic';

    const CHARSET_UTF8 = "UTF-8";
    const CHARSET_ISO_8859_1 = "ISO-8859-1";

    const ENCODING_SCHEMA_NORMAL = 'normal';
    const ENCODING_SCHEMA_UCS2 = 'UCS2';

    const VALIDITY_PERIOD_DEFAULT = 2880;
    const VALIDITY_PERIOD_MIN = 5;

    const MAX_RECIPIENTS = 50000;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->config = $resolver->resolve($options);
    }

    /**
     * @param Sms $sms
     *
     * @return Response[]
     */
    public function send(Sms $sms)
    {
        $messages = [];

        $recipients = $sms->getRecipients();
        if (count($recipients) > self::MAX_RECIPIENTS) {
            foreach (array_chunk($recipients, self::MAX_RECIPIENTS) as $chunk) {
                $message = clone $sms;
                $message
                    ->setRecipients($chunk)
                    ->clearRecipientVariables()
                ;

                foreach ($chunk as $recipient) {
                    foreach ($sms->getRecipientVariables()[$recipient] as $variable => $value) {
                        $message->addRecipientVariable($recipient, $variable, $value);
                    }
                }

                $messages[] = $message;
            }
        } else {
            $messages[] = $sms;
        }

        $responses = [];
        foreach ($messages as $message) {
            $request = $this->prepareRequest($message);

            $responses[] = $this->executeRequest($request);
        }

        return $responses;
    }

    /**
     * @param OptionsResolver $resolver
     */
    private function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'username',
                'password',
                'user_reference',
                'sender_number',
                'method'
            ])
            ->setDefined([
                'delivery_start',
                'validity_period',
                'encoding_scheme',
                'charset',
                'https_enabled'
            ])
            ->setAllowedTypes('username', 'string')
            ->setAllowedTypes('password', 'string')
            ->setAllowedTypes('user_reference', 'string')
            ->setAllowedTypes('sender_number', 'string')
            ->setAllowedTypes('method', 'string')
            ->setAllowedTypes('validity_period', 'int')
            ->setAllowedTypes('encoding_scheme', 'string')
            ->setAllowedTypes('https_enabled', 'bool')
            ->setAllowedValues('method', [
                self::METHOD_CLASSIC,
                self::METHOD_CLASSIC_PLUS,
                self::METHOD_BASIC,
                self::METHOD_TEST_CLASSIC,
                self::METHOD_TEST_CLASSIC_PLUS,
                self::METHOD_TEST_BASIC
            ])
            ->setAllowedValues('delivery_start', function ($value) {
                $d = \DateTime::createFromFormat(\DateTime::RFC2822, $value);
                return $d && $d->format('Y-m-d') === $value;
            })
            ->setAllowedValues('validity_period', function ($value) {
                return $value >= self::VALIDITY_PERIOD_MIN;
            })
            ->setAllowedValues('encoding_scheme', [
                self::ENCODING_SCHEMA_NORMAL,
                self::ENCODING_SCHEMA_UCS2
            ])
            ->setAllowedValues('charset', [
                self::CHARSET_ISO_8859_1,
                self::CHARSET_UTF8
            ])
            ->setDefaults([
                'charset' => self::CHARSET_UTF8,
                'validity_period' => self::VALIDITY_PERIOD_DEFAULT,
                'encoding_schema' => self::ENCODING_SCHEMA_NORMAL,
                'https_enabled' => true
            ])
        ;
    }

    /**
     * @param Sms $sms
     *
     * @return array
     *
     * @throws NoRecipientsSpecifiedException
     */
    private function prepareRequest(Sms $sms)
    {
        if (! $sms->hasRecipients()) {
            throw new NoRecipientsSpecifiedException();
        }

        $request = [
            'username' => $this->config['username'],
            'password' => $this->config['password'],
            'charset' => $this->config['charset'],
            'user_reference' => $this->config['user_reference'],
            'method' => $this->config['method'],
            'sender_number' => $this->config['sender_number'],
            'recipients' => $this->prepareRecipients($sms),
            'text' => $sms->getText()
        ];

        return $request;
    }

    /**
     * @param Sms $sms
     *
     * @return string
     */
    private function prepareRecipients(Sms $sms)
    {
        $recipients = $sms->getRecipients();

        $recipients = array_map(function ($recipient) {
            if ("+" === $recipient[0]) {
                $recipient = substr($recipient, 1);
            } elseif ("00" === substr($recipient, 0, 2)) {
                $recipient = substr($recipient, 2);
            }

            return $recipient;
        }, $recipients);

        $recipientVariables = $sms->getRecipientVariables();

        if (0 === count($recipientVariables)) {
            return json_encode($recipients);
        }

        return json_encode(array_map(function ($recipient) use ($recipientVariables) {
            $targetVariables = $recipientVariables[$recipient];

            return array_merge(['recipient' => $recipient], $targetVariables);
        }, $recipients));
    }

    /**
     * @param array $request
     *
     * @return Response
     */
    private function executeRequest(array $request)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($request));
        curl_setopt(
            $curl,
            CURLOPT_URL,
            $this->config['https_enabled'] ? self::REST_ENDPOINT_HTTPS : self::REST_ENDPOINT_HTTP
        );

        $response = curl_exec($curl);

        curl_close($curl);

        return new Response($response);
    }
}
