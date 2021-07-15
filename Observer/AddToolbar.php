<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
declare(strict_types=1);

namespace Smile\DebugToolbar\Observer;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\Request\Http as MagentoRequest;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\PhpEnvironment\Response as MagentoResponse;
use Smile\DebugToolbar\Block\Toolbar;
use Smile\DebugToolbar\Block\ToolbarFactory;
use Smile\DebugToolbar\Block\Toolbars;
use Smile\DebugToolbar\Block\ToolbarsFactory;
use Smile\DebugToolbar\Helper\Config as HelperConfig;
use Smile\DebugToolbar\Helper\Data as HelperData;
use Smile\DebugToolbar\Helper\Profiler as HelperProfiler;

/**
 * Display the toolbar.
 *
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
     * @var AppState
     */
    protected $appState;

    /**
     * @param ToolbarFactory $blockToolbarFactory
     * @param ToolbarsFactory $blockToolbarsFactory
     * @param HelperData $helperData
     * @param HelperConfig $helperConfig
     * @param HelperProfiler $helperProfiler
     * @param AppState $appState
     */
    public function __construct(
        ToolbarFactory $blockToolbarFactory,
        ToolbarsFactory $blockToolbarsFactory,
        HelperData $helperData,
        HelperConfig $helperConfig,
        HelperProfiler $helperProfiler,
        AppState $appState
    ) {
        $this->blockToolbarFactory = $blockToolbarFactory;
        $this->blockToolbarsFactory = $blockToolbarsFactory;
        $this->helperData = $helperData;
        $this->helperConfig = $helperConfig;
        $this->helperProfiler = $helperProfiler;
        $this->appState = $appState;
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

        // We do not want the toolbar to have an impact on stats => stop the main timer
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
        } catch (Exception $e) {
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
     * @throws LocalizedException
     * @throws FileSystemException
     */
    private function buildToolbar(
        MagentoRequest $request,
        MagentoResponse $response
    ): void {
        // Init the toolbar id
        $this->helperData->initToolbarId($request->getFullActionName());

        // Build the toolbar
        $block = $this->getCurrentExecutionToolbarBlock($request, $response);

        // Save it
        $this->helperData->saveToolbar($block);

        // Keep only the last X executions
        $this->helperData->cleanOldToolbars($this->helperConfig->getNbExecutionToKeep());

        $area = $this->appState->getAreaCode();

        // Add the last toolbars to the content
        if ($area === Area::AREA_FRONTEND || $area === Area::AREA_ADMINHTML && $this->helperConfig->isEnabledAdmin()) {
            $content = $response->getContent();
            $endTag = '</body';
            if (strpos($content, $endTag) !== false) {
                $toolbarsContent = $this->getToolbarsBlock()->toHtml();
                $content = str_replace($endTag, $toolbarsContent . $endTag, $content);
                $response->setContent($content);
            }
        }
    }

    /**
     * Generate the toolbar block for the current execution.
     *
     * @param MagentoRequest $request
     * @param MagentoResponse $response
     * @return Toolbar
     */
    private function getCurrentExecutionToolbarBlock(
        MagentoRequest $request,
        MagentoResponse $response
    ): Toolbar {
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
    private function getToolbarsBlock(): Toolbars
    {
        /** @var Toolbars $block */
        $block = $this->blockToolbarsFactory->create();

        return $block;
    }
}
