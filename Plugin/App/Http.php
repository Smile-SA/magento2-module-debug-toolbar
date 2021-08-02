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
use Smile\DebugToolbar\Helper\Config as ConfigHelper;
use Smile\DebugToolbar\Helper\Data as DataHelper;

/**
 * Start the app_http timer.
 */
class Http
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
     * Add the start time.
     *
     * @param MagentoHttp $subject
     * @return array
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
