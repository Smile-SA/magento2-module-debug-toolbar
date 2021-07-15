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
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\State as AppState;

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
    const KEY_CONFIG_ALLOWED_IPS = 'smile_debugtoolbar/configuration/allowed_ips';
    const KEY_CONFIG_NB_EXECUTION_TO_KEEP = 'smile_debugtoolbar/configuration/keep_last_execution';
    /**#@-*/

    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var array|null
     */
    private $allowedIps;

    /**
     * @param Context $context
     * @param AppState $appState
     */
    public function __construct(Context $context, AppState $appState)
    {
        $this->appState = $appState;
        parent::__construct($context);
    }

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
     * Check whether the IP address of the user is allowed.
     *
     * @return bool
     */
    public function isAllowedIp(): bool
    {
        $allowedIps = $this->getAllowedIps();
        if (empty($allowedIps)) {
            return true;
        }

        $remoteAddr = $this->_remoteAddress->getRemoteAddress();

        return in_array($remoteAddr, $allowedIps, true)
            || in_array($this->_httpHeader->getHttpHost(), $allowedIps, true);
    }

    /**
     * Get the list of allowed ip addresses.
     *
     * @return array
     */
    public function getAllowedIps(): array
    {
        if ($this->allowedIps !== null) {
            return $this->allowedIps;
        }

        $this->allowedIps = [];
        $allowedIps = (string) $this->scopeConfig->getValue(self::KEY_CONFIG_ALLOWED_IPS);

        if ($allowedIps !== '') {
            $this->allowedIps = array_map('trim', explode(',', $allowedIps));
        }

        return $this->allowedIps;
    }

    /**
     * Check whether the profiler can be displayed.
     *
     * @return bool
     */
    public function canDisplay(): bool
    {
        if (!$this->isEnabled() || !$this->isAllowedIp()) {
            return false;
        }

        $enabledAdmin = (bool) $this->scopeConfig->getValue(self::KEY_CONFIG_ENABLE_ADMIN);
        $area = $this->appState->getAreaCode();

        return $enabledAdmin
            ? $area === 'frontend' || $area === 'adminhtml'
            : $area === 'frontend';
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
