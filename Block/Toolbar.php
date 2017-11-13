<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Block;

use Magento\Framework\DataObject;
use Magento\Framework\App\Request\Http             as MagentoRequest;
use Magento\Framework\HTTP\PhpEnvironment\Response as MagentoResponse;
use Magento\Framework\View\Element\AbstractBlock as MagentoAbstractBlock;
use Magento\Framework\View\Element\Context;
use Smile\DebugToolbar\Block\Zone\AbstractZone;
use Smile\DebugToolbar\Block\Zone\SummaryFactory;
use Smile\DebugToolbar\Block\Zone\Summary;
use Smile\DebugToolbar\Helper\Data as HelperData;

/**
 * Main Debug Toolbar Block
 *
 * @author    Laurent MINGUET <lamin@smile.fr>
 * @copyright 2017 Smile
 */
class Toolbar extends MagentoAbstractBlock
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
     * Toolbar constructor.
     *
     * @param Context        $context
     * @param HelperData     $helperData
     * @param SummaryFactory $blockSummaryFactory
     * @param array          $data
     */
    public function __construct(
        Context        $context,
        HelperData     $helperData,
        SummaryFactory $blockSummaryFactory,
        array          $data = []
    ) {
        parent::__construct($context, $data);

        $this->helperData = $helperData;
        $this->blockSummaryFactory = $blockSummaryFactory;

        $this->setData('cache_lifetime', 0);
    }

    /**
     * load the zones
     *
     * @param MagentoRequest  $request
     * @param MagentoResponse $response
     *
     * @return void
     */
    public function loadZones(
        MagentoRequest  $request,
        MagentoResponse $response
    ) {
        /** @var Summary $summaryBlock */
        $summaryBlock = $this->blockSummaryFactory->create();

        $this->zones = [];

        $zones = new DataObject();
        $zones->setData('list', $this->zones);

        $this->_eventManager->dispatch(
            'smile_debug_toolbar_set_zones',
            [
                'zones'         => $zones,
                'summary_block' => $summaryBlock,
                'request'       => $request,
                'response'      => $response,
            ]
        );

        $this->zones = $zones->getData('list');
        $this->zones[] = $summaryBlock;
    }

    /**
     * Get the zones
     *
     * @return Zone\AbstractZone[]
     */
    public function getZones()
    {
        return $this->zones;
    }

    /**
     * Do we have a warning ?
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
     * Get the toolbar id
     *
     * @return string
     */
    public function getToolbarId()
    {
        return $this->helperData->getToolbarId();
    }

    /**
     * Redefine the toHtml method to remove all the cache policy
     *
     * @return string
     */
    public function toHtml()
    {
        $zones = $this->getZones();
        $toolbarId = $this->getToolbarId();

        $parts = explode('-', $toolbarId);
        $date   = $parts[1];
        $area   = $parts[4];
        $action = $parts[5];

        $date = preg_replace(
            '/^([0-9]{4})([0-9]{2})([0-9]{2})_([0-9]{2})([0-9]{2})([0-9]{2})$/',
            '$1-$2-$3 $4:$5:$6',
            $date
        );

        $html = '';

        $html.= '
<div class="smile-toolbar" id="'.$toolbarId.'-toolbar">
    <div class="st-zones" id="'.$toolbarId.'-zones">';

        foreach ($zones as $zone) {
            $html.= $this->getHtmlZoneContent($toolbarId, $zone);
        }

        $html.= '
        <div class="st-navigator" id="'.$toolbarId.'-navigator">...</div>
    </div>
    <div class="st-main">
        <div class="st-selector">
            '.$action.' | '.$area.' | '.$date.' | <span id="'.$toolbarId.'-name">...</span>
            <a onclick="smileToolbarPrevious();">&lt;</a>
            <a onclick="smileToolbarNext();">&gt;</a>
        </div>
        <div class="st-titles" id="'.$toolbarId.'-titles">';

        foreach ($zones as $zone) {
            $html.= $this->getHtmlZoneTitle($toolbarId, $zone);
        }

        $html.= '
        </div>
        <div class="st-logo '.($this->isWarning() ? 'value-warning' : '').'" onclick="smileToolbarMainToggle();">
            Smile ToolBar
        </div>
    </div>
</div>
<script type="text/javascript">
    smileToolbarAdd(\''.$toolbarId.'\', '.($this->isWarning() ? 'true' : 'false').');
</script>';

        return $html;
    }

    /**
     * Get the html content of a zone
     *
     * @param string       $toolbarId
     * @param AbstractZone $zone
     *
     * @return string
     */
    public function getHtmlZoneContent($toolbarId, AbstractZone $zone)
    {
        return '
        <div class="st-zone" id="'.$toolbarId.'-zone-'.$zone->getCode().'">
            <h1>'.$zone->getTitle().'</h1>
            <div class="st-content">
'.$zone->toHtml().'
            </div>
        </div>';
    }

    /**
     * Get the html title of a zone
     *
     * @param string       $toolbarId
     * @param AbstractZone $zone
     *
     * @return string
     */
    public function getHtmlZoneTitle($toolbarId, AbstractZone $zone)
    {
        $htmlId    = $toolbarId.'-title-'.$zone->getCode();

        $htmlClass = 'st-title';
        if ($zone->isWarning()) {
            $htmlClass.= ' value-warning';
        }

        return '
            <div id="'.$htmlId.'" class="'.$htmlClass.'" onclick="smileToolbarZoneSelect(\''.$zone->getCode().'\');">
                '.$zone->getTitle().'
            </div>';
    }
}
