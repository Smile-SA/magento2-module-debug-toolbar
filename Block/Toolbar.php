<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Block;

use Magento\Framework\App\Request\Http as MagentoRequest;
use Magento\Framework\DataObject;
use Magento\Framework\HTTP\PhpEnvironment\Response as MagentoResponse;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use RuntimeException;
use Smile\DebugToolbar\Block\Zone\Summary;
use Smile\DebugToolbar\Block\Zone\SummaryFactory;
use Smile\DebugToolbar\Helper\Data as DataHelper;

/**
 * Main Debug Toolbar Block
 */
class Toolbar extends Template
{
    /**
     * @var Zone\AbstractZone[]
     */
    protected array $zones = [];

    public function __construct(
        Context $context,
        protected DataHelper $dataHelper,
        protected SummaryFactory $blockSummaryFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->setData('cache_lifetime', 0);
        $this->setTemplate('Smile_DebugToolbar::toolbar.phtml');
    }

    /**
     * Load the zones.
     */
    public function loadZones(MagentoRequest $request, MagentoResponse $response): void
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
    public function getZones(): array
    {
        return $this->zones;
    }

    /**
     * Do we have a warning?
     */
    public function isWarning(): bool
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
     * @throws RuntimeException
     */
    public function getToolbarId(): string
    {
        return $this->dataHelper->getToolbarId();
    }

    /**
     * @inheritdoc
     */
    public function toHtml()
    {
        return $this->_toHtml();
    }
}
