<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Observer helper.
 */
class Observer extends AbstractHelper
{
    protected array $eventStats = [];

    /**
     * Init event stat.
     */
    public function initEventStat(string $eventName): void
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
     */
    public function initObserverStat(
        string $eventName,
        string $observerName,
        string $observerInstance,
        bool $observerDisabled
    ): void {
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
     */
    public function addEventStat(string $eventName, float $deltaTime): void
    {
        $eventName = strtolower($eventName);
        $usage = $this->eventStats[$eventName];

        $usage['time_total'] += $deltaTime;
        $usage['time_mean'] = $usage['time_total'] / $usage['nb_call'];

        $this->eventStats[$eventName] = $usage;
    }

    /**
     * Add a stat on observer usage.
     */
    public function addObserverStat(string $eventName, string $observerName, float $deltaTime): void
    {
        $eventName = strtolower($eventName);
        $usage = $this->eventStats[$eventName]['observers'][$observerName];

        $usage['time_total'] += $deltaTime;
        $usage['time_mean'] = $usage['time_total'] / $usage['nb_call'];

        $this->eventStats[$eventName]['observers'][$observerName] = $usage;
    }

    /**
     * Get the event stats.
     */
    public function getEventStats(): array
    {
        return $this->eventStats;
    }
}
