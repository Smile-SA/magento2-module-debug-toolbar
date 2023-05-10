<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Smile\DebugToolbar\Helper\Config as ConfigHelper;

class Loader implements ArgumentInterface
{
    public function __construct(protected ConfigHelper $configHelper)
    {
    }

    /**
     * Check whether the module is enabled.
     */
    public function isToolbarEnabled(): bool
    {
        return $this->configHelper->isEnabled();
    }
}
