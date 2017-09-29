<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Block;

use Magento\Framework\View\Element\AbstractBlock as MagentoAbstractBlock;
use Magento\Framework\View\Element\Context;
use Smile\DebugToolbar\Helper\Data as HelperData;

/**
 * Main Debug Toolbar Block
 * We do not use phtml template files because we do not want to duplicate the templates between FO and BO
 *
 * @author    Laurent MINGUET <lamin@smile.fr>
 * @copyright 2017 Smile
 */
class Toolbars extends MagentoAbstractBlock
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
        $html = '';

        $toolbars = $this->getToolbarList();
        foreach ($toolbars as $toolbarContent) {
            $html.= $toolbarContent."\n";
        }

        $html.= $this->getSmileTableHtml()."\n";
        $html.= '
<script type="text/javascript">
    smileToolbarInit();
</script>
';
        return $html;
    }

    /**
     * Get the smile table html content
     *
     * @return string
     */
    protected function getSmileTableHtml()
    {
        return '
<div class="smile-toolbar" id="st-table-display">
    <div class="st-main" id="st-table-main">
        <div id="st-table-title">...</div>
        <div id="st-table-close" onclick="smileToolbarTableHide()">X</div>
        <div id="st-table-content">...</div>
    </div>
</div>';
    }
}
