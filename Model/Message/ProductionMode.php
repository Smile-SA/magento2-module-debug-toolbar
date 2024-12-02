<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Model\Message;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\State;
use Magento\Framework\Notification\MessageInterface;
use Smile\DebugToolbar\Helper\Config;

/**
 * Add zone blocks to the toolbar.
 */
class ProductionMode implements MessageInterface
{
    public function __construct(
        protected DeploymentConfig $deploymentConfig,
        protected Config $configHelper
    ) {
    }

    /**
     * @inheritdoc
     */
    public function getIdentity()
    {
        return 'smile_debugtoolbar_production_mode';
    }

    /**
     * @inheritdoc
     */
    public function isDisplayed()
    {
        return $this->deploymentConfig->get(State::PARAM_MODE) === State::MODE_PRODUCTION;
    }

    /**
     * @inheritdoc
     */
    public function getText()
    {
        return __(
            <<<'EOT'
                The DebugToolbar module is not supposed to be installed on production environments.
                It may expose sensitive information if it is enabled.
            EOT
        );
    }

    /**
     * @inheritdoc
     */
    public function getSeverity()
    {
        return $this->configHelper->isEnabled()
            ? MessageInterface::SEVERITY_CRITICAL
            : MessageInterface::SEVERITY_MAJOR;
    }
}
