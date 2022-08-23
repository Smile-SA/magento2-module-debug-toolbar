<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\DB\Profiler;

use Exception;
use Zend_Db_Profiler_Query as OriginalProfilerQuery;

/**
 * DB profiler query.
 */
class Query extends OriginalProfilerQuery
{
    /**
     * @var string[]
     */
    protected $trace;

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
}
