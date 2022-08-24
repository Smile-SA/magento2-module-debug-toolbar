<?php

declare(strict_types=1);

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Profiler;
use Smile\DebugToolbar\Helper\Profiler as SmileProfiler;
use Smile\DebugToolbar\Model\Profiler\Driver\Standard\Stat;

if (PHP_SAPI !== 'cli') {
    // We need to declare the stat profiler manually, to use it after
    $options = [
        'drivers' => [
            [
                'output' => false,
                'stat' => new Stat(),
            ],
        ],
    ];

    Profiler::applyConfig($options, BP);
    SmileProfiler::setStat($options['drivers'][0]['stat']);
}

ComponentRegistrar::register(ComponentRegistrar::MODULE, 'Smile_DebugToolbar', __DIR__);
