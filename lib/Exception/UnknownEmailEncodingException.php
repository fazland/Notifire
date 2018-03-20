<?php declare(strict_types=1);

namespace Fazland\Notifire\Exception;

class UnknownEmailEncodingException extends \InvalidArgumentException implements ExceptionInterface
{
    /**
     * @var string
     */
    private $encoding;

    public function __construct(string $encoding, int $code = 0, \Throwable $previous = null)
    {
        $this->encoding = $encoding;

        parent::__construct('Unknown encoding "'.$encoding.'"', $code, $previous);
    }

    /**
     * Gets the unknown encoding.
     *
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }
}
