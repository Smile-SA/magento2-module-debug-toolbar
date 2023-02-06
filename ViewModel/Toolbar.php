<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Smile\DebugToolbar\Helper\Config as ConfigHelper;
use Smile\DebugToolbar\Helper\Data as DataHelper;

class Toolbar implements ArgumentInterface
{
    protected DataHelper $dataHelper;
    protected ConfigHelper $configHelper;

    public function __construct(DataHelper $dataHelper, ConfigHelper $configHelper)
    {
        $this->dataHelper = $dataHelper;
        $this->configHelper = $configHelper;
    }

    /**
     * Check whether the module is enabled.
     */
    public function isToolbarEnabled(): bool
    {
        return $this->configHelper->isEnabled();
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
