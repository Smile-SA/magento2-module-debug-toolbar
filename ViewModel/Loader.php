<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Smile\DebugToolbar\Helper\Config;

class Loader implements ArgumentInterface
{
    protected Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Check whether the module is enabled.
     */
    public function isToolbarEnabled(): bool
    {
        return $this->config->isEnabled();
    }
}
