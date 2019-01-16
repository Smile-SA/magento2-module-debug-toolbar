<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Smile\DebugToolbar\Block\Zone\AbstractZone;
use Smile\DebugToolbar\Block\Zone\CacheFactory;
use Smile\DebugToolbar\Block\Zone\GenericFactory;
use Smile\DebugToolbar\Block\Zone\LayoutFactory;
use Smile\DebugToolbar\Block\Zone\MysqlFactory;
use Smile\DebugToolbar\Block\Zone\ObserverFactory;
use Smile\DebugToolbar\Block\Zone\PreferenceFactory;
use Smile\DebugToolbar\Block\Zone\ProfilerFactory;
use Smile\DebugToolbar\Block\Zone\Request;
use Smile\DebugToolbar\Block\Zone\RequestFactory;
use Smile\DebugToolbar\Block\Zone\Response;
use Smile\DebugToolbar\Block\Zone\ResponseFactory;
use Smile\DebugToolbar\Block\Zone\Summary;

/**
 * Observer Add the Zones
 *
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
 */
class AddZones implements ObserverInterface
{
    /**
     * @var array
     */
    protected $blockFactories = [];

    /**
     * @param CacheFactory $cacheBlockFactory
     * @param GenericFactory $genericBlockFactory
     * @param LayoutFactory $layoutBlockFactory
     * @param MysqlFactory $mysqlBlockFactory
     * @param ObserverFactory $observerBlockFactory
     * @param PreferenceFactory $preferenceBlockFactory
     * @param ProfilerFactory $profilerBlockFactory
     * @param RequestFactory $requestBlockFactory
     * @param ResponseFactory $responseBlockFactory
     */
    public function __construct(
        CacheFactory $cacheBlockFactory,
        GenericFactory $genericBlockFactory,
        LayoutFactory $layoutBlockFactory,
        MysqlFactory $mysqlBlockFactory,
        ObserverFactory $observerBlockFactory,
        PreferenceFactory $preferenceBlockFactory,
        ProfilerFactory $profilerBlockFactory,
        RequestFactory $requestBlockFactory,
        ResponseFactory $responseBlockFactory
    ) {
        $this->blockFactories[] = $genericBlockFactory;
        $this->blockFactories[] = $requestBlockFactory;
        $this->blockFactories[] = $responseBlockFactory;
        $this->blockFactories[] = $layoutBlockFactory;
        $this->blockFactories[] = $mysqlBlockFactory;
        $this->blockFactories[] = $cacheBlockFactory;
        $this->blockFactories[] = $profilerBlockFactory;
        $this->blockFactories[] = $observerBlockFactory;
        $this->blockFactories[] = $preferenceBlockFactory;
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
