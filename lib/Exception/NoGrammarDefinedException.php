<?php declare(strict_types=1);

namespace Fazland\Notifire\Exception;

class NoGrammarDefinedException extends \RuntimeException implements ExceptionInterface
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name, int $code = 0, \Throwable $previous = null)
    {
        $this->name = $name;

        parent::__construct("No such grammar '$name' defined.", $code, $previous);
    }

    /**
     * Gets the grammar name that could not be found.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
