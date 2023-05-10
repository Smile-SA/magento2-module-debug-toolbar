<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Layout;

use Magento\Framework\View\Layout\BuilderInterface;
use Magento\Framework\View\LayoutInterface;

/**
 * Mock of layout builder to avoid layout rebuild.
 */
class Builder implements BuilderInterface
{
    public function __construct(protected LayoutInterface $layout)
    {
    }

    /**
     * @inheritdoc
     */
    public function build()
    {
        return $this->layout;
    }
}
