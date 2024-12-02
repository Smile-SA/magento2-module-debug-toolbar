<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Plugin\App;

use Magento\Framework\App\Http as MagentoHttp;
use Smile\DebugToolbar\Helper\Config as ConfigHelper;
use Smile\DebugToolbar\Helper\Data as DataHelper;

/**
 * Start the app_http timer.
 */
class HttpPlugin
{
    public function __construct(protected DataHelper $dataHelper, protected ConfigHelper $configHelper)
    {
    }

    /**
     * Add the start time.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeLaunch(MagentoHttp $subject): array
    {
        if ($this->configHelper->isEnabledInCurrentArea()) {
            $this->dataHelper->startTimer('app_http');
        }

        return [];
    }
}
