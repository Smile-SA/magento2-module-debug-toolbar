<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
declare(strict_types=1);

namespace Smile\DebugToolbar\Plugin\Event;

use Closure;
use Magento\Framework\Event\InvokerInterface as MagentoInvoker;
use Magento\Framework\Event\Observer as MagentoObserver;
use Smile\DebugToolbar\Helper\Observer as HelperObserver;

/**
 * Fetch observer data.
 */
class Invoker
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
     * @param MagentoInvoker $subject
     * @param Closure $closure
     * @param array $configuration
     * @param MagentoObserver $observer
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        MagentoInvoker $subject,
        Closure $closure,
        array $configuration,
        MagentoObserver $observer
    ) {
        // Note: we can't check if the module is enabled, it could create an infinite loop when fetching cache data
        if (array_key_exists('disabled', $configuration) && $configuration['disabled'] === true) {
            return $closure($configuration, $observer);
        }

        $eventName = $observer->getEvent()->getName();
        $observerInstance = $configuration['instance'];
        $observerName = array_key_exists('name', $configuration) ? $configuration['name'] : $observerInstance;
        $observerDisabled = array_key_exists('disabled', $configuration) ? $configuration['disabled'] : false;

        $this->helperObserver->initObserverStat($eventName, $observerName, $observerInstance, $observerDisabled);

        $startTime = microtime(true);
        $result = $closure($configuration, $observer);
        $deltaTime = microtime(true) - $startTime;

        $this->helperObserver->addObserverStat($eventName, $observerName, $deltaTime);

        return $result;
    }
}
