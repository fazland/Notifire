<?php declare(strict_types=1);

namespace Fazland\Notifire\Result;

class ResultSet
{
    /**
     * @var Result[][]
     */
    private $results = [];

    /**
     * Add a result to the array.
     *
     * @param Result $result
     *
     * @return $this
     */
    public function addResult(Result $result): self
    {
        $handler = $result->getHandlerName();
        $this->results[$handler][] = $result;

        return $this;
    }

    /**
     * Get all Result objects.
     *
     * @return Result[]
     */
    public function all(): array
    {
        $results = [];
        \array_walk_recursive($this->results, function ($v) use (&$results) {
            $results[] = $v;
        });

        return $results;
    }
}
