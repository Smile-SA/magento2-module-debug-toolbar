<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\View\Element\Context;
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
     * @var array|HelperCache
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
     * Get the html of the zone
     *
     * @return string
     */
    public function getZoneHtml()
    {
        $html = '';

        $statSections = $this->getStatsPerAction();
        $list = $this->getCacheUsage();

        foreach ($list as $key => $row) {
            $row['size_total'] = $this->displayHumanSizeKo($row['size_total']);
            $row['size_mean']  = $this->displayHumanSizeKo($row['size_mean']);
            $row['time_total'] = $this->displayHumanTimeMs($row['time_total']);
            $row['time_mean']  = $this->displayHumanTimeMs($row['time_mean']);
            $list[$key] = $row;
        }

        $html.= $this->displayTable(
            'Show All Usage',
            $list,
            [
                'identifier' => [
                    'title' => 'identifier',
                    'class' => '',
                ],
                'nb_call'    => [
                    'title' => 'Nb Call',
                    'class' => 'st-value-number',
                ],
                'size_total' => [
                    'title' => 'Size Total',
                    'class' => 'st-value-unit-ko',
                ],
                'size_mean'  => [
                    'title' => 'Size Mean',
                    'class' => 'st-value-unit-ko',
                ],
                'time_total' => [
                    'title' => 'Time Total',
                    'class' => 'st-value-unit-ms',
                ],
                'time_mean'  => [
                    'title' => 'Time Mean',
                    'class' => 'st-value-unit-ms',
                ],
            ],
            [
                'calls' => 'Calls',
            ]
        );

        $sections = [
            'Types'  => $this->getCacheTypes(),
            'Config' => [
                'Mode'   => [
                    'value'   => $this->getCacheMode(),
                    'warning' => ($this->getCacheMode() !== 'Cm_Cache_Backend_Redis')
                ],
                'Config' => $this->getCacheInfo(),
            ],
        ];

        $sections = array_merge($statSections, $sections);

        $sections['Size'] = [
            'total'  => $this->displayHumanSize($sections['Size']['total']),
            'load'   => $this->displayHumanSize($sections['Size']['load']),
            'save'   => $this->displayHumanSize($sections['Size']['save']),
            'remove' => $this->displayHumanSize($sections['Size']['remove']),
        ];

        $sections['Time'] = [
            'total'  => $this->displayHumanTime($sections['Time']['total']),
            'load'   => $this->displayHumanTime($sections['Time']['load']),
            'save'   => $this->displayHumanTime($sections['Time']['save']),
            'remove' => $this->displayHumanTime($sections['Time']['remove']),
        ];

        $this->addToSummary('Cache', 'Number', $sections['Number']['total']);
        $this->addToSummary('Cache', 'Time', $sections['Time']['total']);
        $this->addToSummary('Cache', 'Size', $sections['Size']['total']);

        $html.= $this->displaySections($sections);

        return $html;
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
}
