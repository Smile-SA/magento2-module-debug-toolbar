<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template\Context;
use Smile\DebugToolbar\Formatter\FormatterFactory;
use Smile\DebugToolbar\Helper\Data as DataHelper;

/**
 * Generic section.
 */
class Generic extends AbstractZone
{
    protected ProductMetadataInterface $productMetadata;
    protected AppState $appState;
    protected DeploymentConfig $deployConfig;

    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        FormatterFactory $formatterFactory,
        ProductMetadataInterface $productMetadata,
        AppState $appState,
        DeploymentConfig $deployConfig,
        array $data = []
    ) {
        parent::__construct($context, $dataHelper, $formatterFactory, $data);
        $this->productMetadata = $productMetadata;
        $this->appState = $appState;
        $this->deployConfig = $deployConfig;
    }

    /**
     * @inheritdoc
     */
    public function getCode(): string
    {
        return 'generic';
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return 'Generic';
    }

    /**
     * Get the product name.
     */
    public function getProductName(): string
    {
        return $this->productMetadata->getName();
    }

    /**
     * Get the product edition.
     */
    public function getProductEdition(): string
    {
        return $this->productMetadata->getEdition();
    }

    /**
     * Get the product version.
     */
    public function getProductVersion(): string
    {
        return $this->productMetadata->getVersion();
    }

    /**
     * Get the magento area.
     */
    public function getMagentoArea(): string
    {
        try {
            return $this->appState->getAreaCode();
        } catch (LocalizedException $e) {
            return '';
        }
    }

    /**
     * Get the magento mode.
     */
    public function getMagentoMode(): string
    {
        return $this->appState->getMode();
    }

    /**
     * Get the session mode.
     */
    public function getSessionMode(): string
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
     * @return array|string
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
     */
    public function getPhpVersion(): string
    {
        return PHP_VERSION;
    }

    /**
     * Get the php memory limit.
     */
    public function getPhpMemoryLimit(): int
    {
        $value = ini_get('memory_limit');
        $value = trim($value);

        $unit = strtolower($value[strlen($value) - 1]);
        $value = (int) substr($value, 0, -1);

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
     */
    public function getPhpMemoryUsed(): int
    {
        return memory_get_peak_usage(true);
    }

    /**
     * Get the php max execution time.
     */
    public function getPhpMaxExecutionTime(): int
    {
        return (int) ini_get('max_execution_time');
    }

    /**
     * Get the php max execution time.
     */
    public function getPhpExecutionTime(): float
    {
        return $this->dataHelper->getTimer('app_http');
    }
}
