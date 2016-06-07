<?php

namespace Fazland\Notifire\Event;

class Events
{
    /**
     * Represents a PreNotifyEvent
     * Dispatched before the handlers are checked and
     * notified.
     *
     * Can be used to add/filter data in notification
     *
     * @const
     */
    const PRE_NOTIFY = 'notifire.pre_notify';

    /**
     * Represents a NotifyEvent
     * Dispatched just before an handler sends the
     * notification
     *
     * Notification is cloned before being passed to
     * the event, so it can be used to modify the
     * notification for a single handler
     *
     * @const
     */
    const NOTIFY = 'notifire.notify';

    /**
     * Represents a PostNotifyEvent
     * Dispatched after the handlers have been notified
     * and the notification is sent
     *
     * If no handler matched the notification this event
     * is not triggered, but an exception is thrown instead
     *
     * @const
     */
    const POST_NOTIFY = 'notifire.post_notify';
}
