<?php

namespace Fazland\Notifire\RestClient\Skebby;

use Fazland\Notifire\Exception\NoRecipientsSpecifiedException;
use Fazland\Notifire\RestClient\Skebby\Constant\Charsets;
use Fazland\Notifire\RestClient\Skebby\Constant\EncodingSchemas;
use Fazland\Notifire\RestClient\Skebby\Constant\Endpoints;
use Fazland\Notifire\RestClient\Skebby\Constant\Recipients;
use Fazland\Notifire\RestClient\Skebby\Constant\SendMethods;
use Fazland\Notifire\RestClient\Skebby\Constant\ValidityPeriods;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class RestClient
{
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
        if (count($recipients) > Recipients::MAX) {
            foreach (array_chunk($recipients, Recipients::MAX) as $chunk) {
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
                'method',
            ])
            ->setDefined([
                'delivery_start',
                'validity_period',
                'encoding_scheme',
                'charset',
                'endpoint_uri',
            ])
            ->setAllowedTypes('username', 'string')
            ->setAllowedTypes('password', 'string')
            ->setAllowedTypes('user_reference', 'string')
            ->setAllowedTypes('sender_number', 'string')
            ->setAllowedTypes('method', 'string')
            ->setAllowedTypes('validity_period', 'int')
            ->setAllowedTypes('encoding_scheme', 'string')
            ->setAllowedTypes('endpoint_uri', 'string')
            ->setAllowedValues('method', [
                SendMethods::CLASSIC,
                SendMethods::CLASSIC_PLUS,
                SendMethods::BASIC,
                SendMethods::TEST_CLASSIC,
                SendMethods::TEST_CLASSIC_PLUS,
                SendMethods::TEST_BASIC,
            ])
            ->setAllowedValues('delivery_start', function ($value) {
                $d = \DateTime::createFromFormat(\DateTime::RFC2822, $value);

                return $d && $d->format('Y-m-d') === $value;
            })
            ->setAllowedValues('validity_period', function ($value) {
                return $value >= ValidityPeriods::MIN && $value <= ValidityPeriods::MAX;
            })
            ->setAllowedValues('encoding_scheme', [
                EncodingSchemas::NORMAL,
                EncodingSchemas::UCS2,
            ])
            ->setAllowedValues('charset', [
                Charsets::ISO_8859_1,
                Charsets::UTF8,
            ])
            ->setDefaults([
                'charset' => Charsets::UTF8,
                'validity_period' => ValidityPeriods::MAX,
                'encoding_schema' => EncodingSchemas::NORMAL,
                'endpoint_uri' => Endpoints::REST_HTTPS,
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
            'text' => $sms->getText(),
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
            if ('+' === $recipient[0]) {
                $recipient = substr($recipient, 1);
            } elseif ('00' === substr($recipient, 0, 2)) {
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
        curl_setopt($curl, CURLOPT_URL, $this->config['endpoint_uri']);

        $response = curl_exec($curl);

        curl_close($curl);

        return new Response($response);
    }
}
