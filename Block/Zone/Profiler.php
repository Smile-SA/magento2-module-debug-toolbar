<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
declare(strict_types=1);

namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\View\Element\Template\Context;
use RuntimeException;
use Smile\DebugToolbar\Formatter\FormatterFactory;
use Smile\DebugToolbar\Helper\Data as HelperData;
use Smile\DebugToolbar\Helper\Profiler as HelperProfiler;

/**
 * Zone for Debug Toolbar Block
 *
 * @api
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
 */
class Profiler extends AbstractZone
{
    /**
     * @var HelperProfiler
     */
    protected $helperProfiler;

    /**
     * @param Context $context
     * @param HelperData $helperData
     * @param FormatterFactory $formatterFactory
     * @param HelperProfiler $helperProfiler
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        FormatterFactory $formatterFactory,
        HelperProfiler $helperProfiler,
        array $data = []
    ) {
        parent::__construct($context, $helperData, $formatterFactory, $data);

        $this->helperProfiler = $helperProfiler;
    }

    /**
     * @inheritdoc
     */
    public function getCode(): string
    {
        return 'profiler';
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return 'Profiler';
    }

    /**
     * Get the profiler timers.
     *
     * @return array
     * @throws RuntimeException
     */
    public function getTimers(): array
    {
        return $this->helperProfiler->getTimers();
    }
}
