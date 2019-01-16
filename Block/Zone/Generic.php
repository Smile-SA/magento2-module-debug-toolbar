<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\State as AppState;
use Magento\Framework\View\Element\Template\Context;
use Smile\DebugToolbar\Formatter\FormatterFactory;
use Smile\DebugToolbar\Helper\Data as HelperData;

/**
 * Zone for Debug Toolbar Block
 *
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
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
     * @param Context $context
     * @param HelperData $helperData
     * @param FormatterFactory $formatterFactory
     * @param ProductMetadataInterface $productMetadata
     * @param AppState $appState
     * @param DeploymentConfig $deployConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        FormatterFactory $formatterFactory,
        ProductMetadataInterface $productMetadata,
        AppState $appState,
        DeploymentConfig $deployConfig,
        array $data = []
    ) {
        parent::__construct($context, $helperData, $formatterFactory, $data);

        $this->productMetadata = $productMetadata;
        $this->appState = $appState;
        $this->deployConfig = $deployConfig;
    }

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return 'generic';
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return 'Generic';
    }

    /**
     * Get the product name.
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->productMetadata->getName();
    }

    /**
     * Get the product edition.
     *
     * @return string
     */
    public function getProductEdition()
    {
        return $this->productMetadata->getEdition();
    }

    /**
     * Get the product version.
     *
     * @return string
     */
    public function getProductVersion()
    {
        return $this->productMetadata->getVersion();
    }

    /**
     * Get the magento area.
     *
     * @return string
     */
    public function getMagentoArea()
    {
        return $this->appState->getAreaCode();
    }

    /**
     * Get the magento mode.
     *
     * @return string
     */
    public function getMagentoMode()
    {
        return $this->appState->getMode();
    }

    /**
     * Get the session mode.
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
     * Get the session info.
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
     * Get the php version.
     *
     * @return string
     */
    public function getPhpVersion()
    {
        return phpversion();
    }

    /**
     * Get the php memory limit.
     *
     * @return int
     */
    public function getPhpMemoryLimit()
    {
        $value = ini_get('memory_limit');
        $value = trim($value);

        $unit = strtolower($value[strlen($value) - 1]);
        $value = (int) substr($value, 0, strlen($value) - 1);

        $units = [
            'k' => 1024,
            'm' => 1024 * 1024,
            'g' => 1024 * 1024 * 1024,
        ];

        if (array_key_exists($unit, $units)) {
            $value *= $units[$unit];
        }

        return $value;
    }

    /**
     * Get the php memory used.
     *
     * @return int
     */
    public function getPhpMemoryUsed()
    {
        return (int) memory_get_peak_usage(true);
    }

    /**
     * Get the php max execution time.
     *
     * @return int
     */
    public function getPhpMaxExecutionTime()
    {
        return (int) ini_get('max_execution_time');
    }

    /**
     * Get the php max execution time.
     *
     * @return string
     */
    public function getPhpExecutionTime()
    {
        return $this->helperData->getTimer('app_http');
    }
}
