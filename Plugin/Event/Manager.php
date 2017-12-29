<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Plugin\Event;

use Magento\Framework\Event\ManagerInterface as MagentoManager;
use Smile\DebugToolbar\Helper\Observer as HelperObserver;

/**
 * Plugin on Event Manager
 *
 * @author    Laurent MINGUET <dirtech@smile.fr>
 * @copyright 2018 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
 */
class Manager
{
    /**
     * @var HelperObserver
     */
    protected $helperObserver;

    /**
     * Invoker constructor.
     *
     * @param HelperObserver $helperObserver
     */
    public function __construct(
        HelperObserver $helperObserver
    ) {
        $this->helperObserver = $helperObserver;
    }

    /**
     * Plugin on Dispatch
     *
     * @param MagentoManager  $subject
     * @param \Closure        $closure
     * @param string          $eventName
     * @param array           $data
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        MagentoManager $subject,
        \Closure       $closure,
        $eventName,
        array $data = []
    ) {

        $this->helperObserver->initEventStat($eventName);

        $startTime = microtime(true);
        $result = $closure($eventName, $data);
        $deltaTime = microtime(true) - $startTime;

        $this->helperObserver->addEventStat($eventName, $deltaTime);

        return $result;
    }
}
