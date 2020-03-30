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
 * Helper: Config
 *
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
 */
class Config extends AbstractHelper
{
    /**#@+
     * Config paths.
     */
    const KEY_CONFIG_ENABLE = 'smile_debugtoolbar/configuration/enabled';
    const KEY_CONFIG_NB_EXECUTION_TO_KEEP = 'smile_debugtoolbar/configuration/keep_last_execution';
    /**#@-*/

    /**
     * Is enabled?
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        $value = (int) $this->scopeConfig->getValue(self::KEY_CONFIG_ENABLE);

        return $value ? true : false;
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
