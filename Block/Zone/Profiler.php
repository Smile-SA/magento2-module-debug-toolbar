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
use Smile\DebugToolbar\Helper\Data as DataHelper;
use Smile\DebugToolbar\Helper\Profiler as ProfilerHelper;

/**
 * Profiler section.
 */
class Profiler extends AbstractZone
{
    /**
     * @var ProfilerHelper
     */
    protected $profilerHelper;

    /**
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param FormatterFactory $formatterFactory
     * @param ProfilerHelper $profilerHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        FormatterFactory $formatterFactory,
        ProfilerHelper $profilerHelper,
        array $data = []
    ) {
        parent::__construct($context, $dataHelper, $formatterFactory, $data);
        $this->profilerHelper = $profilerHelper;
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
        return $this->profilerHelper->getTimers();
    }
}
