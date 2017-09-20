<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Profiler\Driver\Standard\Stat;

/**
 * Helper: Profiler
 *
 * @package   Smile\DebugToolbar\Helper
 * @copyright 2017 Smile
 */
class Profiler extends AbstractHelper
{
    /**
     * @var Stat
     */
    static protected $stat;

    /**
     * @var array
     */
    protected $timers;

    /**
     * Set the Profiler Stat
     *
     * @param Stat $stat
     */
    public static function setStat(Stat $stat)
    {
        self::$stat = $stat;
    }

    /**
     * Get the profiler stat object
     *
     * @return Stat
     * @throws \Exception
     */
    public function getStat()
    {
        if (self::$stat === null) {
            throw new \Exception('Global Smile Profiler Stat is missing');
        }

        return self::$stat;
    }

    /**
     * Compute the stats
     *
     * @return void
     * @throws \Exception
     */
    public function computeStats()
    {
        if ($this->timers !== null) {
            throw new \Exception('Profiler Stats are already computed');
        }

        $this->prepareTimers();
    }

    /**
     * Get the stat timers
     *
     * @return array
     */
    public function getTimers()
    {
        if ($this->timers === null) {
            $this->prepareTimers();
        }

        return $this->timers;
    }

    /**
     * prepare the timers with good sorting
     *
     * @return void
     */
    protected function prepareTimers()
    {
        $this->timers = [];

        $stat = $this->getStat();
        $timerIds = $stat->getFilteredTimerIds();

        $uid = 0;
        foreach ($timerIds as $timerId) {
            $explodedTimerId = explode('->', $timerId);
            $level = count($explodedTimerId)-1;

            $label  = array_pop($explodedTimerId);
            $parent = implode('->', $explodedTimerId);
            $sum    = $stat->fetch($timerId, 'sum');

            $percent = 0;
            if ($timerId === 'magento') {
                $percent = 100;
            }
            if ($parent !== '') {
                $percent = $sum / $this->timers['magento']['sum'] * 100.;
            }

            $timer = [
                'uid'     => $uid,
                'id'      => $timerId,
                'parent'  => null,
                'childs'  => 0,
                'level'   => $level,
                'label'   => $label,
                'sum'     => $sum,
                'percent' => round($percent, 2),
                'avg'     => $stat->fetch($timerId, 'avg'),
                'count'   => $stat->fetch($timerId, 'count'),
            ];

            if ($parent !== '') {
                $timer['parent'] = $this->timers[$parent]['uid'];
                $this->timers[$parent]['childs']++;
            }

            $this->timers[$timer['id']] = $timer;

            $uid++;
        }
    }
}
