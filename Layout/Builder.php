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
 * Mock of Layout Builder to avoid layout rebuild
 *
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
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
    public function __construct(LayoutInterface $layout)
    {
        $this->layout = $layout;
    }

    /**
     * @inheritdoc
     */
    public function build()
    {
        return $this->layout;
    }
}
