<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Block;

use Magento\Framework\View\Element\Template as MagentoTemplateBlock;
use Magento\Framework\View\Element\Template\Context;
use Smile\DebugToolbar\Helper\Data as HelperData;

/**
 * Main Debug Toolbar Block
 *
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
 */
class Toolbars extends MagentoTemplateBlock
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @param Context $context
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->helperData = $helperData;

        $this->setData('cache_lifetime', 0);
        $this->setTemplate('toolbars.phtml');
    }

    /**
     * Return the list of the toolbars.
     *
     * @return \string[]
     */
    public function getToolbarList()
    {
        return $this->helperData->getContentToolbars();
    }

    /**
     * @inheritdoc
     */
    public function toHtml()
    {
        return $this->_toHtml();
    }
}
