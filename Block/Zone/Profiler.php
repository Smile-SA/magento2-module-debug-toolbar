<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\View\Element\Template\Context;
use Smile\DebugToolbar\Formatter\FormatterFactory;
use Smile\DebugToolbar\Helper\Data as HelperData;
use Smile\DebugToolbar\Helper\Profiler as HelperProfiler;

/**
 * Zone for Debug Toolbar Block
 *
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
    public function getCode()
    {
        return 'profiler';
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return 'Profiler';
    }

    /**
     * Get the profiler timers.
     *
     * @return array
     */
    public function getTimers()
    {
        return $this->helperProfiler->getTimers();
    }
}
