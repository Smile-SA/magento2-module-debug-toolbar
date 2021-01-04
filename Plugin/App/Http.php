<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
declare(strict_types=1);

namespace Smile\DebugToolbar\Plugin\App;

use Magento\Framework\App\Http as MagentoHttp;
use Smile\DebugToolbar\Helper\Config as HelperConfig;
use Smile\DebugToolbar\Helper\Data as HelperData;

/**
 * Start the app_http timer.
 */
class Http
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
     * Add the start time.
     *
     * @param MagentoHttp $subject
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeLaunch(MagentoHttp $subject): array
    {
        if ($this->helperConfig->isEnabled()) {
            $this->helperData->startTimer('app_http');
        }

        return [];
    }
}
