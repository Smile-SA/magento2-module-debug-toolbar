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
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\RequestInterface;
use Smile\DebugToolbar\Helper\Config as HelperConfig;

/**
 * Add profiler status to the HTTP context on frontend area when IP restriction is enabled.
 */
class AbstractActionFrontend
{
    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var HelperConfig
     */
    protected $helperConfig;

    /**
     * @param HttpContext $httpContext
     * @param HelperConfig $helperConfig
     */
    public function __construct(HttpContext $httpContext, HelperConfig $helperConfig)
    {
        $this->httpContext = $httpContext;
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
        if (!$this->helperConfig->isEnabled()) {
            return [$request];
        }

        $allowedIps = $this->helperConfig->getAllowedIps();
        if (empty($allowedIps)) {
            return [$request];
        }

        // IP restriction is enabled: add the toolbar display status to the HTTP context
        $this->httpContext->setValue('debug_toolbar', (int) $this->helperConfig->isAllowedIp(), 0);

        return [$request];
    }
}
