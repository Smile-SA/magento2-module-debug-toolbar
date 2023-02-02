<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\DB\Profiler;

use Exception;
use JsonSerializable;
use Smile\DebugToolbar\DB\Profiler;
use Zend_Db_Profiler_Query as OriginalProfilerQuery;

/**
 * DB profiler query.
 */
class Query extends OriginalProfilerQuery implements JsonSerializable
{
    /**
     * @var string[]
     */
    protected array $trace;

    /**
     * @inheritdoc
     */
    public function start(): void
    {
        $this->initTrace();

        parent::start();
    }

    /**
     * Init the trace
     */
    protected function initTrace(): void
    {
        $exception = new Exception();
        $trace = $exception->getTraceAsString();

        // Clean each lines
        $trace = preg_replace("!#[0-9]+\s+!", '', $trace);
        $trace = explode("\n", $trace);

        // Remove the {main} line
        array_pop($trace);

        // Remove the profiler lines
        array_shift($trace);
        array_shift($trace);
        array_shift($trace);

        $this->trace = $trace;
    }

    /**
     * Get the trace.
     *
     * @return string[]
     */
    public function getTrace(): array
    {
        return $this->trace;
    }

    /**
     * Get the query type as string.
     */
    public function getTypeAsString(): string
    {
        switch ($this->getQueryType()) {
            case Profiler::CONNECT:
                return 'connect';
            case Profiler::INSERT:
                return 'insert';
            case Profiler::UPDATE:
                return 'update';
            case Profiler::DELETE:
                return 'delete';
            case Profiler::SELECT:
                return 'select';
            case Profiler::TRANSACTION:
                return 'transaction';
            default:
                return 'query';
        }
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return [
            'query' => $this->getQuery(),
            'type_id' => $this->getQueryType(),
            'type' => $this->getTypeAsString(),
            'time' => $this->getElapsedSecs(),
            'params' => $this->getQueryParams(),
            'trace' => $this->getTrace(),
        ];
    }
}
