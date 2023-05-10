<?php

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
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        FormatterFactory $formatterFactory,
        protected ProfilerHelper $profilerHelper,
        array $data = []
    ) {
        parent::__construct($context, $dataHelper, $formatterFactory, $data);
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
     * @throws RuntimeException
     */
    public function getTimers(): array
    {
        return $this->profilerHelper->getTimers();
    }
}
