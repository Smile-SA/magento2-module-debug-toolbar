<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Block;

use Magento\Framework\App\Request\Http as MagentoRequest;
use Magento\Framework\DataObject;
use Magento\Framework\HTTP\PhpEnvironment\Response as MagentoResponse;
use Magento\Framework\View\Element\Template as MagentoTemplateBlock;
use Magento\Framework\View\Element\Template\Context;
use Smile\DebugToolbar\Block\Zone\Summary;
use Smile\DebugToolbar\Block\Zone\SummaryFactory;
use Smile\DebugToolbar\Helper\Data as HelperData;

/**
 * Main Debug Toolbar Block
 *
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
 */
class Toolbar extends MagentoTemplateBlock
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var Zone\AbstractZone[]
     */
    protected $zones = [];

    /**
     * @var SummaryFactory
     */
    protected $blockSummaryFactory;

    /**
     * @param Context $context
     * @param HelperData $helperData
     * @param SummaryFactory $blockSummaryFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        SummaryFactory $blockSummaryFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->helperData = $helperData;
        $this->blockSummaryFactory = $blockSummaryFactory;

        $this->setData('cache_lifetime', 0);
        $this->setTemplate('toolbar.phtml');
    }

    /**
     * Load the zones.
     *
     * @param MagentoRequest $request
     * @param MagentoResponse $response
     */
    public function loadZones(MagentoRequest $request, MagentoResponse $response)
    {
        /** @var Summary $summaryBlock */
        $summaryBlock = $this->blockSummaryFactory->create();

        $this->zones = [];

        $zones = new DataObject();
        $zones->setData('list', $this->zones);

        $this->_eventManager->dispatch(
            'smile_debug_toolbar_set_zones',
            [
                'zones' => $zones,
                'summary_block' => $summaryBlock,
                'request' => $request,
                'response' => $response,
            ]
        );

        $this->zones = $zones->getData('list');
        $this->zones[] = $summaryBlock;
    }

    /**
     * Get the zones.
     *
     * @return Zone\AbstractZone[]
     */
    public function getZones()
    {
        return $this->zones;
    }

    /**
     * Do we have a warning?
     *
     * @return bool
     */
    public function isWarning()
    {
        $warning = false;

        foreach ($this->getZones() as $zone) {
            if ($zone->isWarning()) {
                $warning = true;
            }
        }

        return $warning;
    }

    /**
     * Get the toolbar id.
     *
     * @return string
     */
    public function getToolbarId()
    {
        return $this->helperData->getToolbarId();
    }

    /**
     * Get the data helper.
     *
     * @return HelperData
     */
    public function getHelperData()
    {
        return $this->helperData;
    }

    /**
     * @inheritdoc
     */
    public function toHtml()
    {
        return $this->_toHtml();
    }
}
