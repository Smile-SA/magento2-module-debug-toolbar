<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Plugin\App;

use Magento\Framework\App\Http as MagentoHttp;
use Smile\DebugToolbar\Helper\Config as HelperConfig;
use Smile\DebugToolbar\Helper\Data as HelperData;

/**
 * Plugin on App
 *
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
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
    public function beforeLaunch(MagentoHttp $subject)
    {
        if ($this->helperConfig->isEnabled()) {
            $this->helperData->startTimer('app_http');
        }

        return [];
    }
}
