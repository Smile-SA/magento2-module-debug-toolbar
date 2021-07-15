<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
declare(strict_types=1);

namespace Smile\DebugToolbar\Plugin\App\Action;

use Magento\Framework\App\Action\AbstractAction as MagentoAction;
use Magento\Framework\App\RequestInterface;
use Smile\DebugToolbar\Helper\Config as HelperConfig;
use Smile\DebugToolbar\Helper\Data as HelperData;

/**
 * Register the action name.
 */
class AbstractAction
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var HelperConfig
     */
    protected $helperConfig;

    /**
     * @param HelperData $helperData
     * @param HelperConfig $helperConfig
     */
    public function __construct(HelperData $helperData, HelperConfig $helperConfig)
    {
        $this->helperData = $helperData;
        $this->helperConfig = $helperConfig;
    }

    /**
     * Plugin on dispatch action.
     *
     * @param MagentoAction $subject
     * @param RequestInterface $request
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeDispatch(MagentoAction $subject, RequestInterface $request): array
    {
        if ($this->helperConfig->isEnabled()) {
            $className = get_class($subject);
            $className = preg_replace('!\\\\Interceptor$!', '', $className);
            $this->helperData->setValue('controller_classname', $className);
        }

        return [$request];
    }
}
