<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Smile\DebugToolbar\Helper\Config as ConfigHelper;
use Smile\DebugToolbar\Helper\Data as DataHelper;

class Toolbar implements ArgumentInterface
{
    public function __construct(protected DataHelper $dataHelper, protected ConfigHelper $configHelper)
    {
    }

    /**
     * Check whether the module is enabled.
     */
    public function isToolbarEnabled(): bool
    {
        return $this->configHelper->isEnabledInCurrentArea();
    }

    /**
     * Get the toolbars list.
     *
     * @return string[]
     */
    public function getToolbarList(): array
    {
        return $this->dataHelper->getContentToolbars();
    }
}
