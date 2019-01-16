<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Helper: Observer
 *
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
 */
class Observer extends AbstractHelper
{
    /**
     * @var array
     */
    protected $eventStats = [];

    /**
     * Init event stat.
     *
     * @param string $eventName
     */
    public function initEventStat($eventName)
    {
        $eventName = strtolower($eventName);
        if (!array_key_exists($eventName, $this->eventStats)) {
            $this->eventStats[$eventName] = [
                'event_name' => $eventName,
                'nb_call' => 0,
                'time_total' => 0,
                'time_mean' => 0,
                'nb_observers' => 0,
                'observers' => [],
            ];
        }

        $this->eventStats[$eventName]['nb_call']++;
    }

    /**
     * Init a stat on observer usage.
     *
     * @param string $eventName
     * @param string $observerName
     * @param string $observerInstance
     * @param bool $observerDisabled
     */
    public function initObserverStat($eventName, $observerName, $observerInstance, $observerDisabled)
    {
        $eventName = strtolower($eventName);
        if (!array_key_exists($observerName, $this->eventStats[$eventName]['observers'])) {
            $this->eventStats[$eventName]['observers'][$observerName] = [
                'observer_name' => $observerName,
                'instance' => $observerInstance,
                'disabled' => $observerDisabled,
                'nb_call' => 0,
                'time_total' => 0,
                'time_mean' => 0,
            ];

            $this->eventStats[$eventName]['nb_observers']++;
        }

        $this->eventStats[$eventName]['observers'][$observerName]['nb_call']++;
    }

    /**
     * Add a stat on event usage.
     *
     * @param string $eventName
     * @param float $deltaTime
     */
    public function addEventStat($eventName, $deltaTime)
    {
        $eventName = strtolower($eventName);
        $usage = $this->eventStats[$eventName];

        $usage['time_total'] += $deltaTime;
        $usage['time_mean'] = $usage['time_total'] / $usage['nb_call'];

        $this->eventStats[$eventName] = $usage;
    }

    /**
     * Add a stat on observer usage.
     *
     * @param string $eventName
     * @param string $observerName
     * @param float $deltaTime
     */
    public function addObserverStat($eventName, $observerName, $deltaTime)
    {
        $eventName = strtolower($eventName);
        $usage = $this->eventStats[$eventName]['observers'][$observerName];

        $usage['time_total'] += $deltaTime;
        $usage['time_mean'] = $usage['time_total'] / $usage['nb_call'];

        $this->eventStats[$eventName]['observers'][$observerName] = $usage;
    }

    /**
     * Get the event stats.
     *
     * @return array
     */
    public function getEventStats()
    {
        return $this->eventStats;
    }
}
