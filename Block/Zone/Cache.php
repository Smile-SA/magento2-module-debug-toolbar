<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\DeploymentConfig;
use Smile\DebugToolbar\Helper\Data as HelperData;
use Smile\DebugToolbar\Helper\Cache as HelperCache;

/**
 * Zone for Debug Toolbar Block
 *
 * @author    Laurent MINGUET <lamin@smile.fr>
 * @copyright 2017 Smile
 */
class Cache extends AbstractZone
{
    /**
     * @var DeploymentConfig
     */
    protected $deployConfig;

    /**
     * @var HelperCache
     */
    protected $helperCache;

    /**
     * Generic constructor.
     *
     * @param Context          $context
     * @param HelperData       $helperData
     * @param DeploymentConfig $deployConfig
     * @param HelperCache      $helperCache
     * @param array            $data
     */
    public function __construct(
        Context          $context,
        HelperData       $helperData,
        DeploymentConfig $deployConfig,
        HelperCache      $helperCache,
        array            $data = []
    ) {
        parent::__construct($context, $helperData, $data);

        $this->deployConfig = $deployConfig;
        $this->helperCache  = $helperCache;
    }

    /**
     * Get the Code
     *
     * @return string
     */
    public function getCode()
    {
        return 'cache';
    }

    /**
     * Get the Title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Cache';
    }

    /**
     * Get the Cache mode
     *
     * @return string
     */
    public function getCacheMode()
    {
        $config = $this->deployConfig->get('cache');
        if (!$config || !is_array($config) || empty($config['frontend']['default']['backend'])) {
            return 'by default';
        }

        return $config['frontend']['default']['backend'];
    }

    /**
     * Get the Cache Info
     *
     * @return string
     */
    public function getCacheInfo()
    {
        $config = $this->deployConfig->get('cache');
        if (!$config || !is_array($config)) {
            return 'empty';
        }

        return $config;
    }

    /**
     * Get the cache types
     *
     * @return array
     */
    public function getCacheTypes()
    {
        return $this->helperCache->getCacheTypes();
    }

    /**
     * Get the cache usage
     *
     * @return array
     */
    public function getCacheUsage()
    {
        return array_values($this->helperCache->getCacheUsage());
    }

    /**
     * Get the usage per action
     *
     * @return array
     */
    public function getStatsPerAction()
    {
        return $this->helperCache->getStatsPerAction();
    }

    /**
     * Prepare calls for display in the table
     *
     * @param array $calls
     *
     * @return string
     */
    public function buildHtmlInfo(array $calls = [])
    {
        $html = "
<table>
    <thead>
        <tr>
            <th>Call Id</th>
            <th>Action</th>
            <th>Size</th>
            <th>Time</th>
        </tr>    
    </thead>
    <tbody>";
        foreach ($calls as $callId => $call) {
            $html.= "
        <tr>
            <td class=\"st-value-number\">".$this->escapeHtml($callId)."</td>
            <td class=\"st-value-center\">".$this->escapeHtml($call['action'])."</td>
            <td class=\"st-value-unit-ko\">".$this->displayHumanSizeKo($call['size'])."</td>
            <td class=\"st-value-unit-ms\">".$this->displayHumanTimeMs($call['time'])."</td>
        </tr>
            ";
        }

        $html.= "
    </tbody>
</table>";

        return $html;
    }
}
