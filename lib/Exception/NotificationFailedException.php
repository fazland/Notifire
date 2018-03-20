<?php declare(strict_types=1);

namespace Fazland\Notifire\Exception;

/**
 * This Exception is raised when:
 * - an error occurs while sending a notification
 * - no notifications were sent.
 */
class NotificationFailedException extends \RuntimeException implements ExceptionInterface
{
    /**
     * @var array
     */
    private $details;

    public function __construct(string $message = '', array $details = [], int $code = 0, \Exception $previous = null)
    {
        $this->details = $details;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getDetails(): array
    {
        return $this->details;
    }
}
