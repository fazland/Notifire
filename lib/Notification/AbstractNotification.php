<?php

namespace Fazland\Notifire\Notification;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Base class for notifications
 * Can be simply extended to create a new notification class
 * or the {@see NotificationTrait} can be used instead
 */
abstract class AbstractNotification implements NotificationInterface
{
    use NotificationTrait;

    /**
     * @var array
     */
    protected $config;

    /**
     * AbstractNotification constructor.
     *
     * @param string $handlerName
     * @param array $options
     */
    public function __construct($handlerName = 'default', array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->config = $resolver->resolve($options);
        $this->handlerName = $handlerName;
    }

    abstract protected function configureOptions(OptionsResolver $resolver);
}
