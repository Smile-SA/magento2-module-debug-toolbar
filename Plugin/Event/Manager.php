<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Plugin\Event;

use Closure;
use Magento\Framework\Event\ManagerInterface as MagentoManager;
use Smile\DebugToolbar\Helper\Observer as HelperObserver;

/**
 * Fetch event data.
 */
class Manager
{
    /**
     * @var HelperObserver
     */
    protected $helperObserver;

    /**
     * @param HelperObserver $helperObserver
     */
    public function __construct(HelperObserver $helperObserver)
    {
        $this->helperObserver = $helperObserver;
    }

    /**
     * Plugin on dispatch.
     *
     * @param MagentoManager $subject
     * @param Closure $closure
     * @param string $eventName
     * @param array $data
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        MagentoManager $subject,
        Closure $closure,
        $eventName,
        array $data = []
    ) {
        // Note: we can't check if the module is enabled, it could create an infinite loop when fetching cache data
        $this->helperObserver->initEventStat((string) $eventName);

        $startTime = microtime(true);
        $result = $closure($eventName, $data);
        $deltaTime = microtime(true) - $startTime;

        $this->helperObserver->addEventStat((string) $eventName, $deltaTime);

        return $result;
    }
}
