<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\DB\Profiler;

use Zend_Db_Profiler_Query as OriginalProfilerQuery;

/**
 * Smile Db Profiler Query
 *
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
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
    public function start()
    {
        $this->initTrace();

        parent::start();
    }

    /**
     * Init the trace
     */
    protected function initTrace()
    {
        $exception = new \Exception();
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
    public function getTrace()
    {
        return $this->trace;
    }
}
