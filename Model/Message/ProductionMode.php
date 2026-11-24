<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Model\Message;

use Magento\Framework\Notification\MessageInterface;
use Smile\DebugToolbar\Helper\Config;

/**
 * Notification message in admin area when the module is installed where it must not be.
 */
class ProductionMode implements MessageInterface
{
    public function __construct(protected Config $configHelper)
    {
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
        return $this->configHelper->isProductionMode() || $this->configHelper->isInstalledOutsideDev();
    }

    /**
     * @inheritdoc
     */
    public function getText()
    {
        return __(
            <<<'EOT'
                The DebugToolbar module must only be installed with "composer require --dev"
                and never on production environments: it may expose sensitive information if it is enabled.
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
