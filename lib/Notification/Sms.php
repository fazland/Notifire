<?php declare(strict_types=1);

namespace Fazland\Notifire\Notification;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Notifire's standard representation of an Sms as an implementation
 * of {@see NotificationInterface}.
 */
class Sms extends AbstractNotification
{
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
     * Sms constructor.
     *
     * @param string $handler
     * @param array  $options
     */
    public function __construct(string $handler = 'default', array $options = [])
    {
        $this->to = [];
        $this->additionalFields = [];
        $this->content = '';

        parent::__construct($handler, $options);
    }

    /**
     * @return string[]
     */
    public function getAdditionalFields(): array
    {
        return $this->additionalFields;
    }

    /**
     * @param string[] $additionalFields
     *
     * @return $this
     */
    public function setAdditionalFields(array $additionalFields): self
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
    public function addAdditionalField(string $key, string $value): self
    {
        $this->additionalFields[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function removeAdditionalField(string $key): self
    {
        if (isset($this->additionalFields[$key])) {
            unset($this->additionalFields[$key]);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return null|string
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
    public function setFrom(string $from): self
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getTo(): array
    {
        return $this->to;
    }

    /**
     * @param string[] $to
     *
     * @return $this
     */
    public function setTo(array $to): self
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @param string $to
     *
     * @return $this
     */
    public function addTo(string $to): self
    {
        $this->to[] = $to;

        return $this;
    }

    /**
     * @param string $to
     *
     * @return $this
     */
    public function removeTo(string $to): self
    {
        $itemPosition = array_search($to, $this->to);

        if (false !== $itemPosition) {
            unset($this->to[$itemPosition]);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'service' => 'default',
        ]);
    }
}
