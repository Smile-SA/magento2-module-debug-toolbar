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
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
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
     * Set the profiler stat.
     *
     * @param Stat $stat
     */
    public static function setStat(Stat $stat)
    {
        self::$stat = $stat;
    }

    /**
     * Get the profiler stat object.
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
     * Compute the stats.
     *
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
     * Get the stat timers.
     *
     * @return array
     * @throws \Exception
     */
    public function getTimers()
    {
        if ($this->timers === null) {
            $this->prepareTimers();
        }

        return $this->timers;
    }

    /**
     * Prepare the timers, with sorting.
     *
     * @throws \Exception
     */
    protected function prepareTimers()
    {
        $this->timers = [];

        $stat = $this->getStat();
        $timerIds = $stat->getFilteredTimerIds();

        $uid = 0;
        foreach ($timerIds as $timerId) {
            $explodedTimerId = explode('->', $timerId);
            $level = count($explodedTimerId) - 1;

            $label = array_pop($explodedTimerId);
            $label = str_replace(BP . '/', '', $label);
            $parent = implode('->', $explodedTimerId);

            $timer = [
                'uid' => $uid,
                'id' => $timerId,
                'parent' => null,
                'children' => 0,
                'level' => $level,
                'label' => $label,
                'sum' => $stat->fetch($timerId, Stat::TIME),
                'avg' => $stat->fetch($timerId, Stat::AVG),
                'count' => $stat->fetch($timerId, Stat::COUNT),
                'mem' => $stat->fetch($timerId, Stat::EMALLOC),
            ];

            if ($timer['mem'] < 0) {
                $timer['mem'] = 0;
            }

            if ($parent !== '') {
                $timer['parent'] = $this->timers[$parent]['uid'];
                $this->timers[$parent]['children']++;
            }

            $this->timers[$timer['id']] = $timer;

            $uid++;
        }

        $this->removeToolbarObserverFromTimers();
        $this->calculatePercents();
    }

    /**
     * Remove the toolbar observer from the profiler timers.
     *
     * @return bool
     */
    protected function removeToolbarObserverFromTimers()
    {
        $toolbarKey = 'OBSERVER:smile_debugtoolbar_add_toolbar_to_response';

        $toolbarTimer = end($this->timers);
        if ($toolbarTimer['label'] !== $toolbarKey) {
            return false;
        }

        $toolbarSum = $toolbarTimer['sum'];
        $toolbarAvg = $toolbarTimer['avg'];
        $keys = explode('->', $toolbarTimer['id']);

        while (count($keys) > 1) {
            array_pop($keys);
            $key = implode('->', $keys);
            $this->timers[$key]['sum'] -= $toolbarSum;
            $this->timers[$key]['avg'] -= $toolbarAvg;
        }

        unset($this->timers[$toolbarTimer['id']]);

        return true;
    }

    /**
     * Calculate the percents.
     */
    protected function calculatePercents()
    {
        foreach ($this->timers as &$timer) {
            $percent = 0;

            if ($timer['id'] === 'magento') {
                $percent = 100;
            }

            if ($timer['parent'] !== null) {
                $percent = $timer['sum'] / $this->timers['magento']['sum'] * 100.;
            }

            $timer['percent'] = round($percent, 2);
        }
    }
}
