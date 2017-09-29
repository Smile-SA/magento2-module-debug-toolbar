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
 * @package   Smile\DebugToolbar\Helper
 * @copyright 2017 Smile
 */
class Observer extends AbstractHelper
{
    /**
     * @var array
     */
    protected $eventStats = [];

    /**
     * Init Event Stat
     *
     * @param string $eventName
     *
     * return void
     */
    public function initEventStat($eventName)
    {
        if (!array_key_exists($eventName, $this->eventStats)) {
            $this->eventStats[$eventName] = [
                'event_name'   => $eventName,
                'nb_call'      => 0,
                'time_total'   => 0,
                'time_mean'    => 0,
                'nb_observers' => 0,
                'observers'    => [],
            ];
        }

        $this->eventStats[$eventName]['nb_call']++;
    }

    /**
     * init a stat on observer usage
     *
     * @param string $eventName
     * @param string $observerName
     * @param string $observerInstance
     * @param bool   $observerDisabled
     *
     * @return void
     */
    public function initObserverStat($eventName, $observerName, $observerInstance, $observerDisabled)
    {
        if (!array_key_exists($observerName, $this->eventStats[$eventName]['observers'])) {
            $this->eventStats[$eventName]['observers'][$observerName] = [
                'observer_name' => $observerName,
                'instance'      => $observerInstance,
                'disabled'      => $observerDisabled,
                'nb_call'       => 0,
                'time_total'    => 0,
                'time_mean'     => 0,
            ];

            $this->eventStats[$eventName]['nb_observers']++;
        }

        $this->eventStats[$eventName]['observers'][$observerName]['nb_call']++;
    }

    /**
     * Add a stat on event usage
     *
     * @param string $eventName
     * @param float  $deltaTime
     *
     * @return void
     */
    public function addEventStat($eventName, $deltaTime)
    {
        $usage = $this->eventStats[$eventName];

        $usage['time_total']+= $deltaTime;
        $usage['time_mean']  = $usage['time_total'] / $usage['nb_call'];

        $this->eventStats[$eventName] = $usage;
    }

    /**
     * Add a stat on observer usage
     *
     * @param string $eventName
     * @param string $observerName
     * @param float  $deltaTime
     *
     * @return void
     */
    public function addObserverStat($eventName, $observerName, $deltaTime)
    {

        $usage = $this->eventStats[$eventName]['observers'][$observerName];

        $usage['time_total']+= $deltaTime;
        $usage['time_mean']  = $usage['time_total'] / $usage['nb_call'];

        $this->eventStats[$eventName]['observers'][$observerName] = $usage;
    }

    /**
     * get the event stats
     *
     * @return array
     */
    public function getEventStats()
    {
        return $this->eventStats;
    }
}
