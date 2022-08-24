<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Config helper.
 */
class Config extends AbstractHelper
{
    /**
     * Check whether the module is enabled.
     */
    public function isEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue('smile_debugtoolbar/configuration/enabled');
    }

    /**
     * Check whether the toolbar can be shown in the admin area.
     */
    public function isEnabledAdmin(): bool
    {
        return (bool) $this->scopeConfig->getValue('smile_debugtoolbar/configuration/enabled_admin');
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
}
