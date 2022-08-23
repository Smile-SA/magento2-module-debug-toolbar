<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Observer;

use Magento\Framework\App\DeploymentConfig\Reader as DeploymentConfigReader;
use Magento\Framework\App\DeploymentConfig\Writer as DeploymentConfigWriter;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\RuntimeException;
use Smile\DebugToolbar\DB\Profiler as DbProfiler;
use Smile\DebugToolbar\Helper\Config as ConfigHelper;

/**
 * Enable the DB profiler.
 */
class EnableDbProfiler implements ObserverInterface
{
    /**
     * @var DeploymentConfigWriter
     */
    protected $deploymentConfigWriter;

    /**
     * @var DeploymentConfigWriter
     */
    protected $deploymentConfigReader;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * @param DeploymentConfigWriter $deploymentConfigWriter
     * @param DeploymentConfigReader $deploymentConfigReader
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        DeploymentConfigWriter $deploymentConfigWriter,
        DeploymentConfigReader $deploymentConfigReader,
        ConfigHelper $configHelper
    ) {
        $this->deploymentConfigWriter = $deploymentConfigWriter;
        $this->deploymentConfigReader = $deploymentConfigReader;
        $this->configHelper = $configHelper;
    }

    /**
     * @inheritdoc
     * @throws FileSystemException
     * @throws RuntimeException
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function execute(Observer $observer)
    {
        $env = $this->deploymentConfigReader->load(ConfigFilePool::APP_ENV);
        $defaultConnection = $env['db']['connection']['default'];
        $enabled = $this->configHelper->isEnabled();

        if ($enabled) {
            $defaultConnection['profiler'] = [
                'class' => DbProfiler::class,
                'enabled' => true,
            ];
        } else {
            unset($defaultConnection['profiler']);
        }

        // Update deployment config only if it was modified
        if ($defaultConnection !== $env['db']['connection']['default']) {
            $env['db']['connection']['default'] = $defaultConnection;
            $this->deploymentConfigWriter->saveConfig([ConfigFilePool::APP_ENV => $env], true);
        }
    }
}
