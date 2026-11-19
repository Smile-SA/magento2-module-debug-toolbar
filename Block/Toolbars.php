<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Main Debug Toolbar Block
 */
class Toolbars extends Template
{
    public function __construct(Context $context, array $data = [])
    {
        $data['template'] = 'toolbars.phtml';
        parent::__construct($context, $data);
        $this->setData('cache_lifetime', 0);
    }

    /**
     * @inheritdoc
     */
    public function toHtml()
    {
        return $this->_toHtml();
    }
}
