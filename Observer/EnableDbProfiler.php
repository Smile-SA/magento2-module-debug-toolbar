<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\DeploymentConfig\Writer as DeploymentConfigWriter;
use Magento\Framework\App\DeploymentConfig\Reader as DeploymentConfigReader;
use Magento\Framework\Config\File\ConfigFilePool;
use Smile\DebugToolbar\Helper\Config as HelperConfig;
use Smile\DebugToolbar\DB\Profiler as DbProfiler;

/**
 * Observer Enable the DbProfiler
 *
 * @author    Laurent MINGUET <dirtech@smile.fr>
 * @copyright 2018 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
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
     * @var HelperConfig
     */
    protected $helperConfig;

    /**
     * PHP Constructor
     *
     * @param DeploymentConfigWriter $deploymentConfigWriter
     * @param DeploymentConfigReader $deploymentConfigReader
     * @param HelperConfig           $helperConfig
     */
    public function __construct(
        DeploymentConfigWriter $deploymentConfigWriter,
        DeploymentConfigReader $deploymentConfigReader,
        HelperConfig           $helperConfig
    ) {
        $this->deploymentConfigWriter = $deploymentConfigWriter;
        $this->deploymentConfigReader = $deploymentConfigReader;
        $this->helperConfig           = $helperConfig;
    }

    /**
     * Execute the observer
     *
     * @param Observer $observer
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Observer $observer)
    {
        $env = $this->deploymentConfigReader->load(ConfigFilePool::APP_ENV);

        unset($env['db']['connection']['default']['profiler']);

        if ($this->helperConfig->isEnabled()) {
            $env['db']['connection']['default']['profiler'] = [
                'class'   => DbProfiler::class,
                'enabled' => true
            ];
        }

        $this->deploymentConfigWriter->saveConfig([ConfigFilePool::APP_ENV => $env], true);
    }
}
