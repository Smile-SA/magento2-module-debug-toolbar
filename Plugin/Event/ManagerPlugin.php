<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Plugin\Event;

use Closure;
use Magento\Framework\Event\ManagerInterface as MagentoManager;
use Smile\DebugToolbar\Helper\Observer as ObserverHelper;

/**
 * Fetch event data.
 */
class ManagerPlugin
{
    public function __construct(protected ObserverHelper $observerHelper)
    {
    }

    /**
     * Plugin on dispatch.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        MagentoManager $subject,
        Closure $closure,
        string $eventName,
        array $data = []
    ): mixed {
        // Note: we can't check if the module is enabled, it could create an infinite loop when fetching cache data
        $this->observerHelper->initEventStat((string) $eventName);

        $startTime = microtime(true);
        $result = $closure($eventName, $data);
        $deltaTime = microtime(true) - $startTime;

        $this->observerHelper->addEventStat((string) $eventName, $deltaTime);

        return $result;
    }
}
