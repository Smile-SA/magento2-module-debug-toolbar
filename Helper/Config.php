<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Helper;

use Magento\Backend\Setup\ConfigOptionsList;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Request\Http;

/**
 * Config helper.
 */
class Config extends AbstractHelper
{
    protected ?bool $isAdmin = null;

    public function __construct(Context $context, protected DeploymentConfig $deploymentConfig)
    {
        parent::__construct($context);
    }

    /**
     * Check whether the module is enabled.
     */
    public function isEnabled(): bool
    {
        $enabled = $this->scopeConfig->isSetFlag('smile_debugtoolbar/configuration/enabled');
        if (!$enabled) {
            return false;
        }

        $enabledAdmin = $this->scopeConfig->isSetFlag('smile_debugtoolbar/configuration/enabled_admin');
        if (!$enabledAdmin && $this->isAdminArea()) {
            return false;
        }

        return true;
    }

    /**
     * Get the config value for keep_last_execution.
     */
    public function getNbExecutionToKeep(): int
    {
        $value = (int) $this->scopeConfig->getValue('smile_debugtoolbar/configuration/keep_last_execution');

        if ($value < 1) {
            $value = 1;
        }

        if ($value > 1024) {
            $value = 1024;
        }

        return $value;
    }

    /**
     * Check whether the current area is adminhtml.
     */
    protected function isAdminArea(): bool
    {
        if ($this->isAdmin === null) {
            // AppState not used because it is set too late in the bootstrap process
            $adminUri = $this->deploymentConfig->get(ConfigOptionsList::CONFIG_PATH_BACKEND_FRONTNAME);

            /** @var Http $request */
            $request = $this->_getRequest();
            $this->isAdmin = str_starts_with($request->getRequestUri(), '/' . $adminUri);
        }

        return $this->isAdmin;
    }
}
