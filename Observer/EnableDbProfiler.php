<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
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
     */
    public function execute(Observer $observer)
    {
        $env = $this->deploymentConfigReader->load(ConfigFilePool::APP_ENV);

        unset($env['db']['connection']['default']['profiler']);

        if ($this->configHelper->isEnabled()) {
            $env['db']['connection']['default']['profiler'] = [
                'class' => DbProfiler::class,
                'enabled' => true,
            ];
        }

        $this->deploymentConfigWriter->saveConfig([ConfigFilePool::APP_ENV => $env], true);
    }
}
