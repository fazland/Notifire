<?php

namespace Fazland\Notifire;

use Fazland\Notification\Email;

/**
 * @author Stefano Rainieri <stefano.rainieri@fazland.com>
 */
class Notifire
{
    /**
     * Creates a new {@see Email} instance, ready to be configured.
     *
     * @param array $options
     *
     * @return Email
     */
    public static function email(array $options)
    {
        return new Email($options);
    }
}