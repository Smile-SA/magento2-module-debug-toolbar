<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Layout;

use Magento\Framework\View\Layout\BuilderInterface;
use Magento\Framework\View\LayoutInterface;

/**
 * Mock of Layout Buildern to avoid layout rebuild
 *
 * @author    Laurent MINGUET <lamin@smile.fr>
 * @copyright 2017 Smile
 */
class Builder implements BuilderInterface
{
    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * @param LayoutInterface $layout
     */
    public function __construct(
        LayoutInterface $layout
    ) {
        $this->layout = $layout;
    }

    /**
     * Build structure
     *
     * @return LayoutInterface
     */
    public function build()
    {
        return $this->layout;
    }
}
