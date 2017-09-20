<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Block\Zone;

use Magento\Framework\View\Element\Template\Context;
use Smile\DebugToolbar\Helper\Data as HelperData;
use Smile\DebugToolbar\Helper\Profiler as HelperProfiler;

/**
 * Zone for Debug Toolbar Block
 *
 * @author    Laurent MINGUET <lamin@smile.fr>
 * @copyright 2017 Smile
 */
class Profiler extends AbstractZone
{
    /**
     * @var HelperProfiler
     */
    protected $helperProfiler;

    /**
     * Profiler constructor
     * .
     * @param Context        $context
     * @param HelperData     $helperData
     * @param HelperProfiler $helperProfiler
     * @param array          $data
     */
    public function __construct(
        Context        $context,
        HelperData     $helperData,
        HelperProfiler $helperProfiler,
        array $data = []
    ) {
        parent::__construct($context, $helperData, $data);

        $this->helperProfiler = $helperProfiler;
    }

    /**
     * Get the Code
     *
     * @return string
     */
    public function getCode()
    {
        return 'profiler';
    }

    /**
     * Get the Title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Profiler';
    }

    /**
     * Get the profiler timers
     *
     * @return array
     */
    public function getTimers()
    {
        return $this->helperProfiler->getTimers();
    }
}
