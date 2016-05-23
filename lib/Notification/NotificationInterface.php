<?php

namespace Fazland\Notifire;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
interface NotificationInterface
{
    /**
     * Implementors MUST dispatch an {@see NotifyEvent}.
     */
    public function send();
}
