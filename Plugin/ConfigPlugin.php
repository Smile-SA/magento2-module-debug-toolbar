<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Plugin;

use Magento\Config\Model\Config;
use Magento\Framework\App\DeploymentConfig\Reader as DeploymentConfigReader;
use Magento\Framework\App\DeploymentConfig\Writer as DeploymentConfigWriter;
use Magento\Framework\Config\File\ConfigFilePool;
use Smile\DebugToolbar\DB\Profiler as DbProfiler;

class ConfigPlugin
{
    protected DeploymentConfigReader $deploymentConfigReader;
    protected DeploymentConfigWriter $deploymentConfigWriter;

    public function __construct(
        DeploymentConfigReader $deploymentConfigReader,
        DeploymentConfigWriter $deploymentConfigWriter
    ) {
        $this->deploymentConfigReader = $deploymentConfigReader;
        $this->deploymentConfigWriter = $deploymentConfigWriter;
    }

    /**
     * Update the profiler section in app/etc/env.php after the config was saved.
     */
    public function afterSave(Config $subject): void
    {
        $enabled = (bool) $subject->getConfigDataValue('smile_debugtoolbar/configuration/enabled');
        $this->toggleProfilerSection($enabled);
    }

    /**
     * Enable or disable the profiler section in app/etc/env.php.
     */
    protected function toggleProfilerSection(bool $enabled): void
    {
        $env = $this->deploymentConfigReader->load(ConfigFilePool::APP_ENV);
        $defaultConnection = $env['db']['connection']['default'];

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
