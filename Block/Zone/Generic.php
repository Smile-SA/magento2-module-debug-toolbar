<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\App\State as AppState;
use Magento\Framework\View\Element\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\DeploymentConfig;
use Smile\DebugToolbar\Helper\Data as HelperData;

/**
 * Zone for Debug Toolbar Block
 *
 * @author    Laurent MINGUET <lamin@smile.fr>
 * @copyright 2017 Smile
 */
class Generic extends AbstractZone
{
    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var AppState
     */
    protected $appState;

    /**
     * @var DeploymentConfig
     */
    protected $deployConfig;

    /**
     * Generic constructor.
     *
     * @param Context                  $context
     * @param HelperData               $helper
     * @param ProductMetadataInterface $productMetadata
     * @param AppState                 $appState
     * @param DeploymentConfig         $deployConfig
     * @param array                    $data
     */
    public function __construct(
        Context                  $context,
        HelperData               $helper,
        ProductMetadataInterface $productMetadata,
        AppState                 $appState,
        DeploymentConfig         $deployConfig,
        array                    $data = []
    ) {
        parent::__construct($context, $helper, $data);

        $this->productMetadata = $productMetadata;
        $this->appState        = $appState;
        $this->deployConfig    = $deployConfig;
    }

    /**
     * Get the Code
     *
     * @return string
     */
    public function getCode()
    {
        return 'generic';
    }

    /**
     * Get the Title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Generic';
    }

    /**
     * Get the html of the zone
     *
     * @return string
     */
    public function getZoneHtml()
    {
        $sections = [
            'Product' => [
                'Product' => $this->getProductName(),
                'Edition' => $this->getProductEdition(),
                'Version' => $this->getProductVersion(),
                'Area'    => $this->getMagentoArea(),
                'Mode'    => $this->getMagentoMode(),
            ],
            'Server' => [
                'PHP Version'            => $this->getPhpVersion(),
                'PHP Memory Limit'       => [
                    'value'   => $this->displayHumanSize($this->getPhpMemoryLimit()),
                    'warning' => $this->getPhpMemoryLimit() < 256*1024*1024,
                ],
                'PHP Memory Used' => [
                    'value'   => $this->displayHumanSize($this->getPhpMemoryUsed()),
                    'warning' => $this->getPhpMemoryUsed() > 128*1024*1024,
                ],
                'PHP Max Execution Time' => [
                    'value'   => $this->displayHumanTime($this->getPhpMaxExecutionTime()),
                    'warning' => $this->getPhpMaxExecutionTime() < 60,
                ],
                'PHP Execution Time' => [
                    'value'   => $this->displayHumanTime($this->getPhpExecutionTime()),
                    'warning' => $this->getPhpExecutionTime() > 5,
                ],
            ],
            'Session' => [
                'Mode'   => [
                    'value'   => $this->getSessionMode(),
                    'warning' => ($this->getSessionMode() !== 'redis'),
                ],
                'Config' => $this->getSessionInfo(),
            ],
        ];

        $this->addToSummary('Server', 'PHP Memory Used', $sections['Server']['PHP Memory Used']);
        $this->addToSummary('Server', 'PHP Execution Time', $sections['Server']['PHP Execution Time']);

        return $this->displaySections($sections);
    }

    /**
     * Get the product name
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->productMetadata->getName();
    }

    /**
     * Get the product edition
     *
     * @return string
     */
    public function getProductEdition()
    {
        return $this->productMetadata->getEdition();
    }

    /**
     * Get the product version
     *
     * @return string
     */
    public function getProductVersion()
    {
        return $this->productMetadata->getVersion();
    }

    /**
     * Get the magento area
     *
     * @return string
     */
    public function getMagentoArea()
    {
        return $this->appState->getAreaCode();
    }

    /**
     * Get the magento mode
     *
     * @return string
     */
    public function getMagentoMode()
    {
        return $this->appState->getMode();
    }

    /**
     * Get the Session mode
     *
     * @return string
     */
    public function getSessionMode()
    {
        $config = $this->deployConfig->get('session');
        if (!$config || !is_array($config) || empty($config['save'])) {
            return 'by default';
        }

        return $config['save'];
    }

    /**
     * Get the Session Info
     *
     * @return string
     */
    public function getSessionInfo()
    {
        $config = $this->deployConfig->get('session');
        if (!$config || !is_array($config)) {
            return 'empty';
        }

        return $config;
    }

    /**
     * Get the php version
     *
     * @return string
     */
    public function getPhpVersion()
    {
        return phpversion();
    }

    /**
     * Get the php memory limit
     *
     * @return int
     */
    public function getPhpMemoryLimit()
    {
        $value = ini_get('memory_limit');
        $value = trim($value);

        $unit = strtolower($value[strlen($value)-1]);
        $value = (int) substr($value, 0, strlen($value)-1);

        $units = [
            'k' => 1024,
            'm' => 1024*1024,
            'g' => 1024*1024*1024,
        ];

        if (array_key_exists($unit, $units)) {
            $value*= $units[$unit];
        }

        return $value;
    }

    /**
     * Get the php memory used
     *
     * @return int
     */
    public function getPhpMemoryUsed()
    {
        return (int) memory_get_peak_usage(true);
    }

    /**
     * Get the php max execution time
     *
     * @return int
     */
    public function getPhpMaxExecutionTime()
    {
        return (int) ini_get('max_execution_time');
    }

    /**
     * Get the php max execution time
     *
     * @return string
     */
    public function getPhpExecutionTime()
    {
        return $this->helperData->getTimer('app_http');
    }
}
