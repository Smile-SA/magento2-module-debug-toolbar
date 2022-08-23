<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Plugin\App\Action;

use Magento\Framework\App\Action\AbstractAction as MagentoAction;
use Magento\Framework\App\RequestInterface;
use Smile\DebugToolbar\Helper\Config as ConfigHelper;
use Smile\DebugToolbar\Helper\Data as DataHelper;

/**
 * Register the action name.
 */
class AbstractAction
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * @param DataHelper $dataHelper
     * @param ConfigHelper $configHelper
     */
    public function __construct(DataHelper $dataHelper, ConfigHelper $configHelper)
    {
        $this->dataHelper = $dataHelper;
        $this->configHelper = $configHelper;
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
        if ($this->configHelper->isEnabled()) {
            $className = get_class($subject);
            $className = preg_replace('!\\\\Interceptor$!', '', $className);
            $this->dataHelper->setValue('controller_classname', $className);
        }

        return [$request];
    }
}
