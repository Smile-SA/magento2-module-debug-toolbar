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
 * @author    Laurent MINGUET <dirtech@smile.fr>
 * @copyright 2018 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
 */
class Toolbars extends MagentoTemplateBlock
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * Toolbar constructor.
     *
     * @param Context    $context
     * @param HelperData $helperData
     * @param array      $data
     */
    public function __construct(
        Context    $context,
        HelperData $helperData,
        array      $data = []
    ) {
        parent::__construct($context, $data);

        $this->helperData = $helperData;

        $this->setData('cache_lifetime', 0);
        $this->setTemplate('toolbars.phtml');
    }

    /**
     * Return the list of the toolbars
     *
     * @return \string[]
     */
    public function getToolbarList()
    {
        return $this->helperData->getContentToolbars();
    }

    /**
     * Redefine the toHtml method to remove all the cache policy
     *
     * @return string
     */
    public function toHtml()
    {
         return $this->_toHtml();
    }
}
