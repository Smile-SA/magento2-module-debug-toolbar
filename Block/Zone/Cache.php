<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\View\Element\Template\Context;
use Smile\DebugToolbar\Formatter\FormatterFactory;
use Smile\DebugToolbar\Helper\Cache as HelperCache;
use Smile\DebugToolbar\Helper\Data as HelperData;

/**
 * Zone for Debug Toolbar Block
 *
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
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
     * @param Context $context
     * @param HelperData $helperData
     * @param FormatterFactory $formatterFactory
     * @param DeploymentConfig $deployConfig
     * @param HelperCache $helperCache
     * @param array $data
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
        $this->helperCache = $helperCache;
    }

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return 'cache';
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return 'Cache';
    }

    /**
     * Get the cache mode.
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
     * Get the cache info.
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
     * Get the cache types.
     *
     * @return array
     */
    public function getCacheTypes()
    {
        return $this->helperCache->getCacheTypes();
    }

    /**
     * Get the cache usage.
     *
     * @return array
     */
    public function getCacheUsage()
    {
        return array_values($this->helperCache->getCacheUsage());
    }

    /**
     * Get the usage per action.
     *
     * @return array
     */
    public function getStatsPerAction()
    {
        return $this->helperCache->getStatsPerAction();
    }

    /**
     * Prepare calls for display in the table.
     *
     * @param array $calls
     * @return string
     */
    public function buildHtmlInfo(array $calls = [])
    {
        $html = '<table>';

        // Table head
        $html .= '<thead><tr>';
        $html .= '<th style="width: 100px">Call Id</th>';
        $html .= '<th>Action</th>';
        $html .= '<th style="width: 120px">Size</th>';
        $html .= '<th style="width: 120px">Time</th>';
        $html .= '</tr></thead>';

        // Table body
        $html .= '<body>';

        foreach ($calls as $callId => $call) {
            $size = $this->formatValue($call['size'], [], 'size_ko');
            $time = $this->formatValue($call['time'], [], 'time_ms');

            $html .= '<tr>';
            $html .= '<td>' . $this->escapeHtml($callId) . '</td>';
            $html .= '<td>' . $this->escapeHtml($call['action']) . '</td>';
            $html .= '<td class="' . $size['css_class'] . '">' . $size['value'] . '</td>';
            $html .= '<td class="' . $time['css_class'] . '">' . $time['value'] . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';

        return $html;
    }
}
