<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Observer;

use Magento\Framework\App\Request\Http as MagentoRequest;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\PhpEnvironment\Response as MagentoResponse;
use Smile\DebugToolbar\Block\Toolbar;
use Smile\DebugToolbar\Block\ToolbarFactory;
use Smile\DebugToolbar\Block\Toolbars;
use Smile\DebugToolbar\Block\ToolbarsFactory;
use Smile\DebugToolbar\Helper\Config as HelperConfig;
use Smile\DebugToolbar\Helper\Data as HelperData;
use Smile\DebugToolbar\Helper\Profiler as HelperProfiler;

/**
 * Observer Add the Toolbar
 *
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
 * @SuppressWarnings(PMD.CouplingBetweenObjects)
 */
class AddToolbar implements ObserverInterface
{
    /**
     * @var ToolbarFactory
     */
    protected $blockToolbarFactory;

    /**
     * @var ToolbarsFactory
     */
    protected $blockToolbarsFactory;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var HelperConfig
     */
    protected $helperConfig;

    /**
     * @var HelperProfiler
     */
    protected $helperProfiler;

    /**
     * @param ToolbarFactory $blockToolbarFactory
     * @param ToolbarsFactory $blockToolbarsFactory
     * @param HelperData $helperData
     * @param HelperConfig $helperConfig
     * @param HelperProfiler $helperProfiler
     */
    public function __construct(
        ToolbarFactory $blockToolbarFactory,
        ToolbarsFactory $blockToolbarsFactory,
        HelperData $helperData,
        HelperConfig $helperConfig,
        HelperProfiler $helperProfiler
    ) {
        $this->blockToolbarFactory = $blockToolbarFactory;
        $this->blockToolbarsFactory = $blockToolbarsFactory;
        $this->helperData = $helperData;
        $this->helperConfig = $helperConfig;
        $this->helperProfiler = $helperProfiler;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function execute(Observer $observer)
    {
        if (!$this->helperConfig->isEnabled()) {
            return;
        }

        // We do not want that the toolbar has a impact on stats => stop the main timer
        $this->helperData->stopTimer('app_http');

        // We do not want that the toolbar has a impact on stats => compute the stat in first
        $this->helperData->startTimer('profiler_build');
        $this->helperProfiler->computeStats();
        $this->helperData->stopTimer('profiler_build');

        /** @var MagentoRequest $request */
        $request = $observer->getEvent()->getData('request');

        /** @var MagentoResponse $response */
        $response = $observer->getEvent()->getData('response');

        // Build the toolbar
        try {
            $this->buildToolbar($request, $response);
        } catch (\Exception $e) {
            // @codingStandardsIgnoreStart
            echo json_encode($e->getMessage());
            exit;
            // @codingStandardsIgnoreEnd
        }
    }

    /**
     * Build the toolbar and add it to the response.
     *
     * @param MagentoRequest $request
     * @param MagentoResponse $response
     */
    protected function buildToolbar(
        MagentoRequest $request,
        MagentoResponse $response
    ) {
        // Init the toolbar id
        $this->helperData->initToolbarId($request->getFullActionName());

        // Build the toolbar
        $block = $this->getCurrentExecutionToolbarBlock($request, $response);

        // Save it
        $this->helperData->saveToolbar($block);

        // Keep only the last X executions
        $this->helperData->cleanOldToolbars($this->helperConfig->getNbExecutionToKeep());

        // Add all the last toolbars to the content
        $content = $response->getContent();
        $endTag = '</body';
        if (strpos($content, $endTag) !== false) {
            $toolbarsContent = $this->getToolbarsBlock()->toHtml();
            $content = str_replace($endTag, $toolbarsContent . $endTag, $content);
            $response->setContent($content);
        }
    }

    /**
     * Generate the toolbar block for the current execution.
     *
     * @param MagentoRequest $request
     * @param MagentoResponse $response
     * @return Toolbar
     */
    protected function getCurrentExecutionToolbarBlock(
        MagentoRequest $request,
        MagentoResponse $response
    ) {
        /** @var Toolbar $block */
        $block = $this->blockToolbarFactory->create();
        $block->loadZones($request, $response);

        return $block;
    }

    /**
     * Generate the toolbars block.
     *
     * @return Toolbars
     */
    protected function getToolbarsBlock()
    {
        /** @var Toolbars $block */
        $block = $this->blockToolbarsFactory->create();

        return $block;
    }
}
