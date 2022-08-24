<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Plugin\App;

use Magento\Framework\App\Http as MagentoHttp;
use Smile\DebugToolbar\Helper\Config as ConfigHelper;
use Smile\DebugToolbar\Helper\Data as DataHelper;

/**
 * Start the app_http timer.
 */
class Http
{
    protected DataHelper $dataHelper;
    protected ConfigHelper $configHelper;

    public function __construct(DataHelper $dataHelper, ConfigHelper $configHelper)
    {
        $this->dataHelper = $dataHelper;
        $this->configHelper = $configHelper;
    }

    /**
     * Add the start time.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeLaunch(MagentoHttp $subject): array
    {
        if ($this->configHelper->isEnabled()) {
            $this->dataHelper->startTimer('app_http');
        }

        return [];
    }
}
