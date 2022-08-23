<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Block;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\View\Element\Template as MagentoTemplateBlock;
use Magento\Framework\View\Element\Template\Context;
use Smile\DebugToolbar\Helper\Data as DataHelper;

/**
 * Main Debug Toolbar Block
 */
class Toolbars extends MagentoTemplateBlock
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param array $data
     */
    public function __construct(Context $context, DataHelper $dataHelper, array $data = [])
    {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;

        $this->setData('cache_lifetime', 0);
        $this->setTemplate('toolbars.phtml');
    }

    /**
     * Return the list of the toolbars.
     *
     * @return string[]
     * @throws FileSystemException
     */
    public function getToolbarList(): array
    {
        return $this->dataHelper->getContentToolbars();
    }

    /**
     * @inheritdoc
     */
    public function toHtml()
    {
        return $this->_toHtml();
    }
}
