<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Smile\DebugToolbar\Block\Zone\AbstractZone;
use Smile\DebugToolbar\Block\Zone\GenericFactory;
use Smile\DebugToolbar\Block\Zone\RequestFactory;
use Smile\DebugToolbar\Block\Zone\ResponseFactory;
use Smile\DebugToolbar\Block\Zone\MysqlFactory;
use Smile\DebugToolbar\Block\Zone\CacheFactory;
use Smile\DebugToolbar\Block\Zone\Request;
use Smile\DebugToolbar\Block\Zone\Response;
use Smile\DebugToolbar\Block\Zone\Summary;

/**
 * Observer Add the Zones
 *
 * @author    Laurent MINGUET <lamin@smile.fr>
 * @copyright 2017 Smile
 */
class AddZones implements ObserverInterface
{
    /**
     * Block Factories
     */
    protected $blockFactories = [];

    /**
     * AddZones constructor.
     * @param GenericFactory  $genericBlockFactory
     * @param RequestFactory  $requestBlockFactory
     * @param ResponseFactory $responseBlockFactory
     * @param MysqlFactory    $mysqlBlockFactory
     * @param CacheFactory    $cacheBlockFactory
     */
    public function __construct(
        GenericFactory  $genericBlockFactory,
        RequestFactory  $requestBlockFactory,
        ResponseFactory $responseBlockFactory,
        MysqlFactory    $mysqlBlockFactory,
        CacheFactory    $cacheBlockFactory
    ) {
        $this->blockFactories[] = $genericBlockFactory;
        $this->blockFactories[] = $requestBlockFactory;
        $this->blockFactories[] = $responseBlockFactory;
        $this->blockFactories[] = $mysqlBlockFactory;
        $this->blockFactories[] = $cacheBlockFactory;
    }

    /**
     * Execute the observer
     *
     * @param Observer $observer Magento Observer Object
     *
     * @return void
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
