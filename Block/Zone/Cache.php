<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
declare(strict_types=1);

namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\View\Element\Template\Context;
use Smile\DebugToolbar\Formatter\FormatterFactory;
use Smile\DebugToolbar\Helper\Cache as CacheHelper;
use Smile\DebugToolbar\Helper\Data as DataHelper;

/**
 * Cache section.
 */
class Cache extends AbstractZone
{
    /**
     * @var DeploymentConfig
     */
    protected $deployConfig;

    /**
     * @var CacheHelper
     */
    protected $cacheHelper;

    /**
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param FormatterFactory $formatterFactory
     * @param DeploymentConfig $deployConfig
     * @param CacheHelper $cacheHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        FormatterFactory $formatterFactory,
        DeploymentConfig $deployConfig,
        CacheHelper $cacheHelper,
        array $data = []
    ) {
        parent::__construct($context, $dataHelper, $formatterFactory, $data);
        $this->deployConfig = $deployConfig;
        $this->cacheHelper = $cacheHelper;
    }

    /**
     * @inheritdoc
     */
    public function getCode(): string
    {
        return 'cache';
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return 'Cache';
    }

    /**
     * Get the cache mode.
     *
     * @return string
     */
    public function getCacheMode(): string
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
     * @return string|array
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
    public function getCacheTypes(): array
    {
        return $this->cacheHelper->getCacheTypes();
    }

    /**
     * Get the cache usage.
     *
     * @return array
     */
    public function getCacheUsage(): array
    {
        return array_values($this->cacheHelper->getCacheUsage());
    }

    /**
     * Get the usage per action.
     *
     * @return array
     */
    public function getStatsPerAction(): array
    {
        return $this->cacheHelper->getStatsPerAction();
    }

    /**
     * Prepare calls for display in the table.
     *
     * @param array $calls
     * @return string
     */
    public function buildHtmlInfo(array $calls = []): string
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
