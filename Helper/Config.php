<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
declare(strict_types=1);

namespace Smile\DebugToolbar\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Config helper.
 */
class Config extends AbstractHelper
{
    /**#@+
     * Config paths.
     */
    const KEY_CONFIG_ENABLE = 'smile_debugtoolbar/configuration/enabled';
    const KEY_CONFIG_ENABLE_ADMIN = 'smile_debugtoolbar/configuration/enabled_admin';
    const KEY_CONFIG_NB_EXECUTION_TO_KEEP = 'smile_debugtoolbar/configuration/keep_last_execution';
    /**#@-*/

    /**
     * Check whether the module is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(self::KEY_CONFIG_ENABLE);
    }

    /**
     * Check whether the toolbar can be shown in the admin area.
     *
     * @return bool
     */
    public function isEnabledAdmin(): bool
    {
        return (bool) $this->scopeConfig->getValue(self::KEY_CONFIG_ENABLE_ADMIN);
    }

    /**
     * Get the config value for keep_last_execution.
     *
     * @return int
     */
    public function getNbExecutionToKeep(): int
    {
        $value = (int) $this->scopeConfig->getValue(self::KEY_CONFIG_NB_EXECUTION_TO_KEEP);

        if ($value < 1) {
            $value = 1;
        }

        if ($value > 1024) {
            $value = 1024;
        }

        return $value;
    }
}
