<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Smile\DebugToolbar\Block\Zone\AbstractZone;
use Smile\DebugToolbar\Block\Zone\Request;
use Smile\DebugToolbar\Block\Zone\Response;
use Smile\DebugToolbar\Block\Zone\Summary;

/**
 * Add zone blocks to the toolbar.
 */
class AddZones implements ObserverInterface
{
    public function __construct(protected array $blockFactories)
    {
    }

    /**
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        $list = $observer->getEvent()->getData('zones')->getData('list');

        /** @var Summary $summaryBlock */
        $summaryBlock = $observer->getEvent()->getData('summary_block');

        foreach ($this->blockFactories as $blockFactory) {
            /** @var AbstractZone $block */
            $block = $blockFactory->create();
            $block->setSummaryBlock($summaryBlock);

            if ($block->getCode() === 'request') {
                /** @var Request $block */
                $block->setRequest($observer->getEvent()->getData('request'));
            }

            if ($block->getCode() === 'response') {
                /** @var Response $block */
                $block->setResponse($observer->getEvent()->getData('response'));
            }

            $list[] = $block;
        }

        $observer->getEvent()->getData('zones')->setData('list', $list);
    }
}
