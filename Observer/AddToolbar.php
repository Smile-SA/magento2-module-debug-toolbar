<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Observer;

use Exception;
use Magento\Framework\App\Request\Http as MagentoRequest;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\PhpEnvironment\Response as MagentoResponse;
use Smile\DebugToolbar\Block\Toolbar;
use Smile\DebugToolbar\Block\ToolbarFactory;
use Smile\DebugToolbar\Block\ToolbarsFactory;
use Smile\DebugToolbar\Helper\Config as ConfigHelper;
use Smile\DebugToolbar\Helper\Data as DataHelper;
use Smile\DebugToolbar\Helper\Profiler as ProfilerHelper;

/**
 * Display the toolbar.
 *
 * @SuppressWarnings(PMD.CouplingBetweenObjects)
 */
class AddToolbar implements ObserverInterface
{
    protected ToolbarFactory $blockToolbarFactory;
    protected ToolbarsFactory $blockToolbarsFactory;
    protected DataHelper $dataHelper;
    protected ConfigHelper $configHelper;
    protected ProfilerHelper $profilerHelper;
    protected AppState $appState;

    public function __construct(
        ToolbarFactory $blockToolbarFactory,
        ToolbarsFactory $blockToolbarsFactory,
        DataHelper $dataHelper,
        ConfigHelper $configHelper,
        ProfilerHelper $profilerHelper,
        AppState $appState
    ) {
        $this->blockToolbarFactory = $blockToolbarFactory;
        $this->blockToolbarsFactory = $blockToolbarsFactory;
        $this->dataHelper = $dataHelper;
        $this->configHelper = $configHelper;
        $this->profilerHelper = $profilerHelper;
        $this->appState = $appState;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function execute(Observer $observer)
    {
        if (!$this->configHelper->isEnabled()) {
            return;
        }

        // We do not want the toolbar to have an impact on stats => stop the main timer
        $this->dataHelper->stopTimer('app_http');

        // We do not want that the toolbar has a impact on stats => compute the stat in first
        $this->dataHelper->startTimer('profiler_build');
        $this->profilerHelper->computeStats();
        $this->dataHelper->stopTimer('profiler_build');

        /** @var MagentoRequest $request */
        $request = $observer->getEvent()->getData('request');
        if ($request->getRouteName() === 'smile_debug_toolbar') {
            return;
        }

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
     * @throws LocalizedException
     * @throws FileSystemException
     */
    private function buildToolbar(
        MagentoRequest $request,
        MagentoResponse $response
    ): void {
        // Init the toolbar id
        $this->dataHelper->initToolbarId($request->getFullActionName());

        // Build the toolbar
        $block = $this->getCurrentExecutionToolbarBlock($request, $response);

        // Save it
        $this->dataHelper->saveToolbar($block);

        // Keep only the last X executions
        $this->dataHelper->cleanOldToolbars($this->configHelper->getNbExecutionToKeep());
    }

    /**
     * Generate the toolbar block for the current execution.
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
}
