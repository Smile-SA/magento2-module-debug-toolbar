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
use Smile\DebugToolbar\Helper\Data  as HelperData;
use Smile\DebugToolbar\Formatter\FormatterFactory;
use Smile\DebugToolbar\Helper\Cache as HelperCache;

/**
 * Zone for Debug Toolbar Block
 *
 * @author    Laurent MINGUET <dirtech@smile.fr>
 * @copyright 2018 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
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
     * @param FormatterFactory $formatterFactory
     * @param DeploymentConfig $deployConfig
     * @param HelperCache      $helperCache
     * @param array            $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        FormatterFactory $formatterFactory,
        DeploymentConfig $deployConfig,
        HelperCache $helperCache,
        array $data = []
    ) {
        parent::__construct($context, $helperData, $formatterFactory, $data);

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
            <th style=\"width: 100px\">Call Id</th>
            <th >Action</th>
            <th style=\"width: 120px\">Size</th>
            <th style=\"width: 120px\">Time</th>
        </tr>    
    </thead>
    <tbody>";
        foreach ($calls as $callId => $call) {
            $size = $this->formatValue($call['size'], [], 'size_ko');
            $time = $this->formatValue($call['time'], [], 'time_ms');

            $html.= "
        <tr>
            <td class=\"\">".$this->escapeHtml($callId)."</td>
            <td class=\"\">".$this->escapeHtml($call['action'])."</td>
            <td class=\"".$size['css_class']."\">".$size['value']."</td>
            <td class=\"".$time['css_class']."\">".$time['value']."</td>
        </tr>
            ";
        }

        $html.= "
    </tbody>
</table>";

        return $html;
    }
}
