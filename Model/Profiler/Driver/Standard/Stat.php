<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
declare(strict_types=1);

namespace Smile\DebugToolbar\Model\Profiler\Driver\Standard;

use Magento\Framework\Profiler;
use Magento\Framework\Profiler\Driver\Standard\Stat as BaseStat;

/**
 * Overridden because core class is not compatible with PHP 8.1...
 */
class Stat extends BaseStat
{
    /**
     * @inheritdoc
     */
    protected function _getOrderedTimerIds()
    {
        $timerIds = array_keys($this->_timers);
        if (count($timerIds) <= 2) {
            /* No sorting needed */
            return $timerIds;
        }

        /* Prepare PCRE once to use it inside the loop body */
        $nestingSep = preg_quote(Profiler::NESTING_SEPARATOR, '/');
        $patternLastTimerId = '/' . $nestingSep . '(?:.(?!' . $nestingSep . '))+$/';

        $prevTimerId = $timerIds[0];
        $result = [$prevTimerId];
        for ($i = 1; $i < count($timerIds); $i++) {
            $timerId = $timerIds[$i];
            /* Skip already added timer */
            if (!$timerId) {
                continue;
            }
            /* Loop over all timers that need to be closed under previous timer */
            while (strpos($timerId, $prevTimerId . Profiler::NESTING_SEPARATOR) !== 0) {
                /* Add to result all timers nested in the previous timer */
                for ($j = $i + 1; $j < count($timerIds); $j++) {
                    // REWRITE: check null value before calling strpos
                    if ($timerIds[$j] !== null
                        && strpos($timerIds[$j], $prevTimerId . Profiler::NESTING_SEPARATOR) === 0
                    ) {
                        $result[] = $timerIds[$j];
                        /* Mark timer as already added */
                        $timerIds[$j] = null;
                    }
                }
                /* Go to upper level timer */
                $count = 0;
                $prevTimerId = preg_replace($patternLastTimerId, '', $prevTimerId, -1, $count);
                /* Break the loop if no replacements was done. It is possible when we are */
                /* working with top level (root) item */
                if (!$count) {
                    break;
                }
            }
            /* Add current timer to the result */
            $result[] = $timerId;
            $prevTimerId = $timerId;
        }
        return $result;
    }
}
