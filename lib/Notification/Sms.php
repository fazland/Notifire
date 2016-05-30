<?php

namespace Fazland\Notifire\Notification;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Daniele Rapisarda <daniele.rapisarda@fazland.com>
 *
 * Notifire's standard representation of an Sms as an implementation
 * of {@see NotificationInterface}.
 */
class Sms implements NotificationInterface
{
    use NotificationTrait;

    /**
     * @var string
     */
    private $from;

    /**
     * @var string[]
     */
    private $to;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string[]
     */
    private $additionalFields;

    /**
     * @var string[]
     */
    private $config;

    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->config = $resolver->resolve($options);
    }

    /**
     * @return string[]
     */
    public function getAdditionalFields()
    {
        return $this->additionalFields;
    }

    /**
     * @param string[] $additionalFields
     *
     * @return $this
     */
    public function setAdditionalFields($additionalFields)
    {
        $this->additionalFields = $additionalFields;

        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function addAdditionalField($key, $value)
    {
        $this->additionalFields[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function removeAdditionalField($key)
    {
        if (isset($this->additionalFields[$key])) {
            unset($this->additionalFields[$key]);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string $from
     *
     * @return $this
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string[] $to
     *
     * @return $this
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @param string $to
     *
     * @return $this
     */
    public function addTo($to)
    {
        $this->to[] = $to;

        return $this;
    }

    /**
     * @param string $to
     *
     * @return $this
     */
    public function removeTo($to)
    {
        $itemPosition = array_search($to, $this->to);

        if ($itemPosition) {
            unset($this->to[$itemPosition]);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'provider' => 'twilio',
            'account_name' => 'default'
        ]);
    }
}
